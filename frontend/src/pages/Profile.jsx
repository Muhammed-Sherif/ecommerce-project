import React from 'react'
import { ProfileAPI } from '../api'
import Header from '../components/Header'

export default function Profile() {
  const user = JSON.parse(localStorage.getItem('user') || 'null')
  const [saved, setSaved] = React.useState(false)
  const [error, setError] = React.useState('')
  const [loading, setLoading] = React.useState(true)

  const [form, setForm] = React.useState({
    full_name: '',
    phone: '',
    street: '',
    city: '',
    state: '',
    country: '',
    zip_code: ''
  })

  React.useEffect(() => {
    const fetchShipping = async () => {
      try {
        const { data } = await ProfileAPI.getShipping()
        if (data?.success && data.shipping) {
          setForm((prev) => ({ ...prev, ...data.shipping }))
        }
      } catch (err) {
        console.error('Failed to load shipping info:', err)
      } finally {
        setLoading(false)
      }
    }
    fetchShipping()
  }, [])

  const handleChange = (e) => {
    const { name, value } = e.target
    setForm((prev) => ({ ...prev, [name]: value }))
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    setSaved(false)
    setError('')

    if (!form.street || !form.city || !form.country || !form.zip_code) {
      setError('Please complete required shipping fields.')
      return
    }

    try {
      const { data } = await ProfileAPI.updateShipping(form)
      if (!data?.success) {
        throw new Error(data?.message || 'Failed to save shipping info')
      }
      setSaved(true)
    } catch (err) {
      setError(err?.message || 'Failed to save shipping info')
    }
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-3xl mx-auto px-6 lg:px-10 py-10">
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h1 className="text-2xl font-semibold text-gray-900 mb-1">Profile</h1>
          <p className="text-sm text-gray-600 mb-6">
            {user ? `Signed in as ${user.email || user.name}` : 'Manage your account'}
          </p>

          <h2 className="text-lg font-semibold text-gray-900 mb-4">Shipping Information</h2>

          {loading && (
            <div className="mb-4 text-sm text-gray-500">Loading shipping info...</div>
          )}

          {error && (
            <div className="mb-4 text-sm text-red-600">{error}</div>
          )}
          {saved && (
            <div className="mb-4 text-sm text-green-600">Shipping info saved.</div>
          )}

          <form onSubmit={handleSubmit} className="grid gap-4">
            <div className="grid md:grid-cols-2 gap-4">
              <div>
                <label className="block text-sm text-gray-700 mb-1">Full Name</label>
                <input
                  name="full_name"
                  value={form.full_name}
                  onChange={handleChange}
                  className="w-full px-3 py-2 border rounded focus:ring-teal-500 focus:border-teal-500"
                  placeholder="Full name"
                />
              </div>
              <div>
                <label className="block text-sm text-gray-700 mb-1">Phone</label>
                <input
                  name="phone"
                  value={form.phone}
                  onChange={handleChange}
                  className="w-full px-3 py-2 border rounded focus:ring-teal-500 focus:border-teal-500"
                  placeholder="Phone number"
                />
              </div>
            </div>

            <div>
              <label className="block text-sm text-gray-700 mb-1">Street Address *</label>
              <input
                name="street"
                value={form.street}
                onChange={handleChange}
                className="w-full px-3 py-2 border rounded focus:ring-teal-500 focus:border-teal-500"
                placeholder="Street address"
                required
              />
            </div>

            <div className="grid md:grid-cols-3 gap-4">
              <div>
                <label className="block text-sm text-gray-700 mb-1">City *</label>
                <input
                  name="city"
                  value={form.city}
                  onChange={handleChange}
                  className="w-full px-3 py-2 border rounded focus:ring-teal-500 focus:border-teal-500"
                  placeholder="City"
                  required
                />
              </div>
              <div>
                <label className="block text-sm text-gray-700 mb-1">State</label>
                <input
                  name="state"
                  value={form.state}
                  onChange={handleChange}
                  className="w-full px-3 py-2 border rounded focus:ring-teal-500 focus:border-teal-500"
                  placeholder="State"
                />
              </div>
              <div>
                <label className="block text-sm text-gray-700 mb-1">Zip Code *</label>
                <input
                  name="zip_code"
                  value={form.zip_code}
                  onChange={handleChange}
                  className="w-full px-3 py-2 border rounded focus:ring-teal-500 focus:border-teal-500"
                  placeholder="Zip code"
                  required
                />
              </div>
            </div>

            <div>
              <label className="block text-sm text-gray-700 mb-1">Country *</label>
              <input
                name="country"
                value={form.country}
                onChange={handleChange}
                className="w-full px-3 py-2 border rounded focus:ring-teal-500 focus:border-teal-500"
                placeholder="Country"
                required
              />
            </div>

            <div className="flex justify-end">
              <button
                type="submit"
                className="px-6 py-2 bg-teal-600 text-white font-medium rounded hover:bg-teal-700 transition"
              >
                Save Shipping Info
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  )
}
