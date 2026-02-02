import React from 'react'
import { useLocation } from 'react-router-dom'
import { ProfileAPI } from '../api'
import Header from '../components/Header'

export default function Profile() {
  const location = useLocation()
  const isAdminProfile = location.pathname.startsWith('/admin')
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
  const requiredFields = ['full_name', 'phone', 'street', 'city', 'country', 'zip_code']
  const filledRequired = requiredFields.filter((field) => `${form[field] || ''}`.trim()).length
  const completion = Math.round((filledRequired / requiredFields.length) * 100)
  const initials =
    user?.name
      ?.split(' ')
      .map((part) => part[0])
      .join('')
      .slice(0, 2)
      .toUpperCase() ||
    user?.email?.slice(0, 2)?.toUpperCase() ||
    'ME'

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

    if (!form.full_name || !form.phone || !form.street || !form.city || !form.country || !form.zip_code) {
      setError('Please complete all required fields.')
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
    <div className="min-h-screen bg-slate-50">
      {!isAdminProfile && <Header />}
      <div className="relative isolate">
        <div className="absolute inset-0 -z-10 overflow-hidden">
          <div className="absolute -top-28 right-0 h-72 w-72 rounded-full bg-teal-200/40 blur-3xl" />
          <div className="absolute -bottom-24 left-0 h-72 w-72 rounded-full bg-amber-200/40 blur-3xl" />
          <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent" />
        </div>
        <div className={`max-w-6xl mx-auto ${isAdminProfile ? 'px-4 py-4' : 'px-6 lg:px-10 py-10 lg:py-14'}`}>
          <div className="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
              <h1 className="text-3xl md:text-4xl font-semibold tracking-tight text-slate-900 font-serif">
                Profile & Shipping
              </h1>
              <p className="text-sm md:text-base text-slate-600 mt-2">
                {user ? `Signed in as ${user.email || user.name}` : 'Manage your account details and delivery info.'}
              </p>
            </div>
            <div className="flex items-center gap-3 bg-white/80 border border-slate-200 rounded-2xl px-4 py-3 shadow-sm">
              <div className="text-xs uppercase tracking-wide text-slate-500">Profile completion</div>
              <div className="w-28 h-2 rounded-full bg-slate-100 overflow-hidden">
                <div className="h-full bg-teal-500" style={{ width: `${completion}%` }} />
              </div>
              <div className="text-sm font-semibold text-slate-700">{completion}%</div>
            </div>
          </div>

          <div className="mt-8 grid gap-8 lg:grid-cols-[280px,1fr]">
            <aside className="space-y-6">
              <div className="bg-white/90 border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div className="flex items-center gap-4">
                  <div className="h-14 w-14 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-700 text-white flex items-center justify-center text-lg font-semibold">
                    {initials}
                  </div>
                  <div>
                    <div className="text-sm text-slate-500">Account</div>
                    <div className="text-lg font-semibold text-slate-900">
                      {user?.name || user?.email || 'Guest user'}
                    </div>
                  </div>
                </div>
                <div className="mt-5 border-t border-slate-100 pt-4">
                  <div className="text-xs uppercase tracking-wide text-slate-500 mb-3">Contact</div>
                  <div className="text-sm text-slate-700">{form.full_name || 'Add your full name'}</div>
                  <div className="text-sm text-slate-500">{form.phone || 'Add a phone number'}</div>
                </div>
              </div>

              <div className="bg-white/90 border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div className="flex items-center justify-between">
                  <div className="text-sm font-semibold text-slate-900">Shipping status</div>
                  <span
                    className={`text-xs font-semibold px-2.5 py-1 rounded-full ${
                      completion === 100
                        ? 'bg-emerald-100 text-emerald-700'
                        : 'bg-amber-100 text-amber-700'
                    }`}
                  >
                    {completion === 100 ? 'Complete' : 'Incomplete'}
                  </span>
                </div>
                <p className="mt-2 text-sm text-slate-600">
                  Add your full delivery details to speed up checkout.
                </p>
                <div className="mt-4 text-xs text-slate-500">
                  Required fields: Street, City, Country, Zip code.
                </div>
              </div>
            </aside>

            <section className="bg-white/95 border border-slate-200 rounded-2xl p-6 md:p-8 shadow-sm">
              <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                  <h2 className="text-xl font-semibold text-slate-900">Shipping Information</h2>
                  <p className="text-sm text-slate-600">This address is used for order deliveries.</p>
                </div>
                <div className="text-xs text-slate-500">Fields marked * are required.</div>
              </div>

              {loading && (
                <div className="mb-4 text-sm text-slate-500">Loading shipping info...</div>
              )}

              {error && (
                <div className="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                  {error}
                </div>
              )}
              {saved && (
                <div className="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                  Shipping info saved.
                </div>
              )}

              <form onSubmit={handleSubmit} className="grid gap-5">
                <div className="grid md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-sm font-medium text-slate-700 mb-1">Full Name *</label>
                    <input
                      name="full_name"
                      value={form.full_name}
                      onChange={handleChange}
                      className="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-800 focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500 outline-none transition"
                      placeholder="Full name"
                      required
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-slate-700 mb-1">Phone *</label>
                    <input
                      name="phone"
                      value={form.phone}
                      onChange={handleChange}
                      className="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-800 focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500 outline-none transition"
                      placeholder="Phone number"
                      required
                    />
                  </div>
                </div>

                <div>
                  <label className="block text-sm font-medium text-slate-700 mb-1">Street Address *</label>
                  <input
                    name="street"
                    value={form.street}
                    onChange={handleChange}
                    className="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-800 focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500 outline-none transition"
                    placeholder="Street address"
                    required
                  />
                </div>

                <div className="grid md:grid-cols-3 gap-4">
                  <div>
                    <label className="block text-sm font-medium text-slate-700 mb-1">City *</label>
                    <input
                      name="city"
                      value={form.city}
                      onChange={handleChange}
                      className="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-800 focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500 outline-none transition"
                      placeholder="City"
                      required
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-slate-700 mb-1">State</label>
                    <input
                      name="state"
                      value={form.state}
                      onChange={handleChange}
                      className="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-800 focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500 outline-none transition"
                      placeholder="State"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-slate-700 mb-1">Zip Code *</label>
                    <input
                      name="zip_code"
                      value={form.zip_code}
                      onChange={handleChange}
                      className="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-800 focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500 outline-none transition"
                      placeholder="Zip code"
                      required
                    />
                  </div>
                </div>

                <div>
                  <label className="block text-sm font-medium text-slate-700 mb-1">Country *</label>
                  <input
                    name="country"
                    value={form.country}
                    onChange={handleChange}
                    className="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-800 focus:ring-2 focus:ring-teal-500/30 focus:border-teal-500 outline-none transition"
                    placeholder="Country"
                    required
                  />
                </div>

                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2">
                  <div className="text-xs text-slate-500">
                    We use this address for shipping updates and delivery.
                  </div>
                  <button
                    type="submit"
                    className="inline-flex items-center justify-center px-6 py-2.5 rounded-xl bg-teal-600 text-white font-semibold shadow-sm hover:bg-teal-700 transition"
                  >
                    Save Shipping Info
                  </button>
                </div>
              </form>
            </section>
          </div>
        </div>
      </div>
    </div>
  )
}
