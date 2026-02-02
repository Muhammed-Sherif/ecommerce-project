import React, { useEffect, useState } from 'react'
import { ProductsAPI } from '../../api'
import { alertError, confirm } from '../../utils/alert'

const emptyForm = {
  name: '',
  description: '',
  price: '',
  quantity: '',
  category: 'general',
  status: 'active',
  image: ''
}

export default function AdminProducts() {
  const [products, setProducts] = useState([])
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState('')
  const [form, setForm] = useState(emptyForm)
  const [editingId, setEditingId] = useState(null)
  const [editForm, setEditForm] = useState(emptyForm)
  const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'

  const resolveImageUrl = (url) => {
    if (!url) return ''
    if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('data:') || url.startsWith('blob:')) {
      return url
    }
    const base = apiBaseUrl.replace(/\/$/, '')
    const path = url.startsWith('/') ? url : `/${url}`
    return `${base}${path}`
  }

  const fetchProducts = async () => {
    setLoading(true)
    setError('')
    try {
      const { data } = await ProductsAPI.getAllForDashboard()
      const list = data?.products || data || []
      setProducts(Array.isArray(list) ? list : [])
    } catch (err) {
      setError(err?.response?.data?.message || err?.message || 'Failed to load products')
      setProducts([])
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    fetchProducts()
  }, [])

  const handleChange = (key, value) => {
    setForm((prev) => ({ ...prev, [key]: value }))
  }

  const handleImageChange = (e) => {
    const file = e.target.files[0]
    if (file) {
      setForm(prev => ({ ...prev, image: file, imagePreview: URL.createObjectURL(file) }))
    }
  }

  const handleEditChange = (key, value) => {
    setEditForm((prev) => ({ ...prev, [key]: value }))
  }

  const handleEditImageChange = (e) => {
    const file = e.target.files[0]
    if (file) {
      setEditForm(prev => ({ ...prev, image: file, imagePreview: URL.createObjectURL(file) }))
    }
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setSaving(true)
    setError('')
    try {
      // Create FormData for file upload
      const formData = new FormData()
      formData.append('name', form.name.trim())
      formData.append('description', form.description)
      formData.append('price', Number(form.price))
      formData.append('quantity', Number(form.quantity || 0))
      formData.append('category', form.category || 'general')
      formData.append('status', form.status || 'active')
      if (form.image) {
        formData.append('image', form.image)
      }

      const { data } = await ProductsAPI.create(formData)
      if (!data?.success) {
        throw new Error(data?.message || 'Failed to create product')
      }
      await fetchProducts()
      setForm(emptyForm)
    } catch (err) {
      setError(err?.response?.data?.message || err?.message || 'Failed to create product')
    } finally {
      setSaving(false)
    }
  }

  const startEdit = (product) => {
    setEditingId(product.id)
    setEditForm({
      name: product.name || '',
      description: product.description || '',
      price: product.price ?? '',
      quantity: product.quantity ?? '',
      category: product.category || 'general',
      status: product.status || 'active',
      image: null,
      imagePreview: resolveImageUrl(
        product.image || product.image_url || (Array.isArray(product.images) ? product.images[0] : '')
      )
    })
  }

  const cancelEdit = () => {
    setEditingId(null)
    setEditForm(emptyForm)
  }

  const handleUpdate = async (e) => {
    e.preventDefault()
    if (!editingId) return
    setSaving(true)
    setError('')
    try {
      // Create FormData for file upload
      const formData = new FormData()
      formData.append('name', editForm.name.trim())
      formData.append('description', editForm.description)
      formData.append('price', Number(editForm.price))
      formData.append('quantity', Number(editForm.quantity || 0))
      formData.append('category', editForm.category || 'general')
      formData.append('status', editForm.status || 'active')
      if (editForm.image) {
        formData.append('image', editForm.image)
      }

      const { data } = await ProductsAPI.update(editingId, formData)
      if (!data?.success) {
        throw new Error(data?.message || 'Failed to update product')
      }
      await fetchProducts()
      cancelEdit()
    } catch (err) {
      setError(err?.response?.data?.message || err?.message || 'Failed to update product')
    } finally {
      setSaving(false)
    }
  }

  const handleDelete = async (id) => {
    const ok = await confirm({ title: 'Delete this product?', text: 'This action cannot be undone.' })
    if (!ok) return
    try {
      await ProductsAPI.remove(id)
      setProducts((prev) => prev.filter((p) => p.id !== id))
    } catch (err) {
      alertError('Failed to delete product', err?.response?.data?.message || err?.message)
    }
  }

  return (
    <div className="space-y-8">
      <div className="bg-white rounded-lg border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Add New Product</h2>
        {error && <p className="text-sm text-red-600 mb-4">{error}</p>}
        <form onSubmit={handleSubmit} className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input
              type="text"
              value={form.name}
              onChange={(e) => handleChange('name', e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg"
              required
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <input
              type="text"
              value={form.category}
              onChange={(e) => handleChange('category', e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg"
            />
          </div>
          <div className="md:col-span-2">
            <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea
              value={form.description}
              onChange={(e) => handleChange('description', e.target.value)}
              rows={3}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg"
            />
          </div>
          <div className="md:col-span-2">
            <label className="block text-sm font-medium text-gray-700 mb-1">Product Image</label>
            <input
              type="file"
              accept="image/*"
              onChange={handleImageChange}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg"
            />
            {form.imagePreview && (
              <div className="mt-2">
                <img src={resolveImageUrl(form.imagePreview)} alt="Preview" className="h-20 w-20 object-cover rounded" />
              </div>
            )}
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Price</label>
            <input
              type="number"
              min="0"
              step="0.01"
              value={form.price}
              onChange={(e) => handleChange('price', e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg"
              required
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
            <input
              type="number"
              min="0"
              step="1"
              value={form.quantity}
              onChange={(e) => handleChange('quantity', e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg"
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select
              value={form.status}
              onChange={(e) => handleChange('status', e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg"
            >
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div className="md:col-span-2">
            <button
              type="submit"
              disabled={saving}
              className="px-5 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 disabled:bg-teal-400"
            >
              {saving ? 'Saving...' : 'Create Product'}
            </button>
          </div>
        </form>
      </div>

      {editingId && (
        <div className="bg-white rounded-lg border border-gray-200 p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold text-gray-900">Edit Product</h2>
            <button
              onClick={cancelEdit}
              className="text-sm text-gray-600 hover:text-gray-900"
            >
              Cancel
            </button>
          </div>
          {error && <p className="text-sm text-red-600 mb-4">{error}</p>}
          <form onSubmit={handleUpdate} className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Name</label>
              <input
                type="text"
                value={editForm.name}
                onChange={(e) => handleEditChange('name', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg"
                required
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Category</label>
              <input
                type="text"
                value={editForm.category}
                onChange={(e) => handleEditChange('category', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg"
              />
            </div>
            <div className="md:col-span-2">
              <label className="block text-sm font-medium text-gray-700 mb-1">Description</label>
              <textarea
                value={editForm.description}
                onChange={(e) => handleEditChange('description', e.target.value)}
                rows={3}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg"
              />
            </div>
            <div className="md:col-span-2">
              <label className="block text-sm font-medium text-gray-700 mb-1">Product Image</label>
              <input
                type="file"
                accept="image/*"
                onChange={handleEditImageChange}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg"
              />
              {editForm.imagePreview && (
                <div className="mt-2">
                  <img src={resolveImageUrl(editForm.imagePreview)} alt="Current" className="h-20 w-20 object-cover rounded" />
                </div>
              )}
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Price</label>
              <input
                type="number"
                min="0"
                step="0.01"
                value={editForm.price}
                onChange={(e) => handleEditChange('price', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg"
                required
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
              <input
                type="number"
                min="0"
                step="1"
                value={editForm.quantity}
                onChange={(e) => handleEditChange('quantity', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select
                value={editForm.status}
                onChange={(e) => handleEditChange('status', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg"
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div className="md:col-span-2">
              <button
                type="submit"
                disabled={saving}
                className="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-blue-400"
              >
                {saving ? 'Saving...' : 'Update Product'}
              </button>
            </div>
          </form>
        </div>
      )}

      <div className="bg-white rounded-lg border border-gray-200">
        <div className="px-6 py-4 border-b border-gray-200">
          <h2 className="text-lg font-semibold text-gray-900">All Products</h2>
        </div>
        {loading ? (
          <div className="p-6 text-sm text-gray-600">Loading products...</div>
        ) : products.length === 0 ? (
          <div className="p-6 text-sm text-gray-600">No products found.</div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {products.map((product) => (
                  <tr key={product.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4 text-sm text-gray-900 font-medium">{product.name}</td>
                    <td className="px-6 py-4 text-sm text-gray-600">
                      {product.image ? (
                        <img
                          src={resolveImageUrl(product.image)}
                          alt={product.name}
                          className="h-10 w-10 rounded object-cover border border-gray-200"
                        />
                      ) : (
                        <span className="text-gray-400">—</span>
                      )}
                    </td>
                    <td className="px-6 py-4 text-sm text-gray-600">{product.category || 'general'}</td>
                    <td className="px-6 py-4 text-sm text-gray-900">${Number(product.price || 0).toFixed(2)}</td>
                    <td className="px-6 py-4 text-sm text-gray-600">{product.quantity ?? 0}</td>
                    <td className="px-6 py-4 text-sm text-gray-600">{product.status || 'active'}</td>
                    <td className="px-6 py-4 text-sm">
                      <div className="flex items-center gap-3">
                        <button
                          onClick={() => startEdit(product)}
                          className="text-blue-600 hover:text-blue-800 font-medium"
                        >
                          Edit
                        </button>
                        <button
                          onClick={() => handleDelete(product.id)}
                          className="text-red-600 hover:text-red-800 font-medium"
                        >
                          Delete
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  )
}
