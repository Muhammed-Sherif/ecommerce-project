import React, { useEffect, useState } from 'react'
import { InventoryAPI, ProductsAPI } from '../../api'

export default function AdminInventory() {
  const [products, setProducts] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [editingId, setEditingId] = useState(null)
  const [saving, setSaving] = useState(false)
  const [editForm, setEditForm] = useState({ stock: '' })

  const fetchProducts = async () => {
    setLoading(true)
    setError('')
    try {
      const { data } = await ProductsAPI.getAll()
      const list = data?.products || data || []
      const baseProducts = Array.isArray(list) ? list : []
      const withInventory = await Promise.all(
        baseProducts.map(async (product) => {
          try {
            const { data: invData } = await InventoryAPI.getByProduct(product.id)
            const inventory = invData?.inventory
            return {
              ...product,
              stock: inventory?.quantity ?? 0,
              reserved_quantity: inventory?.reserved_quantity ?? 0
            }
          } catch {
            return {
              ...product,
              stock: 0,
              reserved_quantity: 0
            }
          }
        })
      )
      setProducts(withInventory)
    } catch (err) {
      setError(err?.response?.data?.message || err?.message || 'Failed to load inventory')
      setProducts([])
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    fetchProducts()
  }, [])

  const startEdit = (product) => {
    setEditingId(product.id)
    setEditForm({
      stock: product.stock ?? 0
    })
  }

  const cancelEdit = () => {
    setEditingId(null)
    setEditForm({ stock: '' })
  }

  const handleEditChange = (key, value) => {
    setEditForm((prev) => ({ ...prev, [key]: value }))
  }

  const handleUpdate = async (e) => {
    e.preventDefault()
    if (!editingId) return
    setSaving(true)
    setError('')
    try {
      const desired = Number(editForm.stock || 0)
      const current = Number(products.find((p) => p.id === editingId)?.stock ?? 0)
      const delta = desired - current
      if (delta !== 0) {
        const { data } = await InventoryAPI.adjust({
          product_id: editingId,
          quantity: delta,
          reason: 'Manual inventory adjustment'
        })
        if (!data?.success) {
          throw new Error(data?.error || data?.message || 'Failed to update inventory')
        }
      }
      await fetchProducts()
      cancelEdit()
    } catch (err) {
      setError(err?.response?.data?.message || err?.message || 'Failed to update inventory')
    } finally {
      setSaving(false)
    }
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Inventory</h1>
        <p className="text-sm text-gray-600">Track stock levels for all products.</p>
      </div>

      <div className="flex items-center justify-between">
        {error && <p className="text-sm text-red-600">{error}</p>}
        <button
          onClick={fetchProducts}
          className="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50"
        >
          Refresh
        </button>
      </div>

      {editingId && (
        <div className="bg-white rounded-lg border border-gray-200 p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold text-gray-900">Edit Inventory</h2>
            <button
              onClick={cancelEdit}
              className="text-sm text-gray-600 hover:text-gray-900"
            >
              Cancel
            </button>
          </div>
          <form onSubmit={handleUpdate} className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Stock</label>
              <input
                type="number"
                min="0"
                step="1"
                value={editForm.stock}
                onChange={(e) => handleEditChange('stock', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg"
              />
            </div>
            <div className="md:col-span-2">
              <button
                type="submit"
                disabled={saving}
                className="px-5 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 disabled:bg-teal-400"
              >
                {saving ? 'Saving...' : 'Update Inventory'}
              </button>
            </div>
          </form>
        </div>
      )}

      <div className="bg-white rounded-lg border border-gray-200">
        <div className="px-6 py-4 border-b border-gray-200">
          <h2 className="text-lg font-semibold text-gray-900">Stock Levels</h2>
        </div>
        {loading ? (
          <div className="p-6 text-sm text-gray-600">Loading inventory...</div>
        ) : products.length === 0 ? (
          <div className="p-6 text-sm text-gray-600">No products found.</div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {products.map((product) => {
                  const stock = Number(product.stock ?? 0)
                  const lowStock = stock <= 5
                  return (
                    <tr key={product.id} className="hover:bg-gray-50">
                      <td className="px-6 py-4 text-sm text-gray-900 font-medium">{product.name}</td>
                      <td className="px-6 py-4 text-sm text-gray-600">{product.category || 'general'}</td>
                      <td className="px-6 py-4 text-sm">
                        <span className={`${lowStock ? 'text-red-600 font-semibold' : 'text-gray-700'}`}>
                          {stock}
                        </span>
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-600">{product.status || 'active'}</td>
                      <td className="px-6 py-4 text-sm">
                        <button
                          onClick={() => startEdit(product)}
                          className="text-blue-600 hover:text-blue-800 font-medium"
                        >
                          Edit
                        </button>
                      </td>
                    </tr>
                  )
                })}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  )
}
