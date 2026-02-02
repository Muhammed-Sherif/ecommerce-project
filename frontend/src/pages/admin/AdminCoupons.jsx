import React, { useEffect, useState } from 'react'
import { CouponAPI } from '../../api'
import { alertError, alertSuccess, confirm } from '../../utils/alert'

const emptyForm = {
  code: '',
  value: '',
  type: 'percentage', // percentage | fixed
  usage_limit: '',
  expires_at: '',
  is_active: true
}

export default function AdminCoupons() {
  const [coupons, setCoupons] = useState([])
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [error, setError] = useState('')
  const [form, setForm] = useState(emptyForm)

  const fetchCoupons = async () => {
    setLoading(true)
    setError('')
    try {
      const { data } = await CouponAPI.getAll()
      const list = data?.coupons || data || []
      setCoupons(Array.isArray(list) ? list : [])
    } catch (err) {
      setError(err?.response?.data?.message || err?.message || 'Failed to load coupons')
      setCoupons([])
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    fetchCoupons()
  }, [])

  const handleChange = (key, value) => {
    setForm((prev) => ({ ...prev, [key]: value }))
  }

  const handleCreate = async (e) => {
    e.preventDefault()
    setSaving(true)
    setError('')
    try {
      const payload = {
        code: form.code.trim().toUpperCase(),
        value: Number(form.value),
        type: form.type,
        usage_limit: form.usage_limit === '' ? null : Number(form.usage_limit),
        expires_at: form.expires_at || null,
        is_active: !!form.is_active
      }
      const { data } = await CouponAPI.create(payload)
      if (!data?.success) throw new Error(data?.message || 'Failed to create coupon')
      await fetchCoupons()
      setForm(emptyForm)
      alertSuccess('Coupon Created', 'The coupon has been created successfully.')
    } catch (err) {
      setError(err?.response?.data?.message || err?.message || 'Failed to create coupon')
      alertError('Failed to create coupon', err?.response?.data?.message || err?.message)
    } finally {
      setSaving(false)
    }
  }

  const handleDelete = async (id) => {
    const ok = await confirm({ title: 'Delete this coupon?', text: 'This action cannot be undone.' })
    if (!ok) return
    try {
      await CouponAPI.remove(id)
      setCoupons((prev) => prev.filter((c) => c.id !== id))
    } catch (err) {
      alertError('Failed to delete coupon', err?.response?.data?.message || err?.message)
    }
  }

  return (
    <div className="space-y-8">
      <div className="bg-white rounded-lg border border-gray-200 p-6">
        <h2 className="text-lg font-semibold text-gray-900 mb-4">Create Coupon</h2>
        {error && <p className="text-sm text-red-600 mb-4">{error}</p>}
        <form onSubmit={handleCreate} className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Code</label>
            <input type="text" value={form.code} onChange={(e) => handleChange('code', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg" required />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Discount</label>
            <input type="number" min="0" step="0.01" value={form.value} onChange={(e) => handleChange('value', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg" required />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Type</label>
            <select value={form.type} onChange={(e) => handleChange('type', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg">
              <option value="percentage">Percentage</option>
              <option value="fixed">Fixed</option>
            </select>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Expires At</label>
            <input type="date" value={form.expires_at} onChange={(e) => handleChange('expires_at', e.target.value)} className="w-full px-3 py-2 border border-gray-300 rounded-lg" />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Usage Limit</label>
            <input
              type="number"
              min="0"
              step="1"
              value={form.usage_limit}
              onChange={(e) => handleChange('usage_limit', e.target.value)}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg"
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select value={form.is_active ? 'active' : 'inactive'} onChange={(e) => handleChange('is_active', e.target.value === 'active')} className="w-full px-3 py-2 border border-gray-300 rounded-lg">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <div className="md:col-span-2">
            <button type="submit" disabled={saving} className="px-5 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 disabled:bg-teal-400">
              {saving ? 'Saving...' : 'Create Coupon'}
            </button>
          </div>
        </form>
      </div>

      <div className="bg-white rounded-lg border border-gray-200">
        <div className="px-6 py-4 border-b border-gray-200">
          <h2 className="text-lg font-semibold text-gray-900">All Coupons</h2>
        </div>
        {loading ? (
          <div className="p-6 text-sm text-gray-600">Loading coupons...</div>
        ) : coupons.length === 0 ? (
          <div className="p-6 text-sm text-gray-600">No coupons found.</div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {coupons.map((c) => (
                  <tr key={c.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4 text-sm font-medium text-gray-900">{c.code}</td>
                    <td className="px-6 py-4 text-sm text-gray-700">{Number(c.value || 0).toFixed(2)}</td>
                    <td className="px-6 py-4 text-sm text-gray-700">{c.type || 'fixed'}</td>
                    <td className="px-6 py-4 text-sm text-gray-700">{c.is_active ? 'active' : 'inactive'}</td>
                    <td className="px-6 py-4 text-sm text-gray-700">{c.expires_at || '-'}</td>
                    <td className="px-6 py-4 text-sm">
                      <button onClick={() => handleDelete(c.id)} className="text-red-600 hover:text-red-800 font-medium">Delete</button>
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
