import React, { useEffect, useMemo, useState } from 'react'
import Header from '../components/Header'
import { OrdersAPI } from '../api'

export default function Orders() {
  const [orders, setOrders] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')

  const summary = useMemo(() => {
    const total = orders.length
    const delivered = orders.filter((order) => (order.status || '').toLowerCase() === 'delivered').length
    const pending = orders.filter((order) => (order.status || '').toLowerCase() === 'pending').length
    return { total, delivered, pending }
  }, [orders])

  useEffect(() => {
    const fetchOrders = async () => {
      try {
        const { data } = await OrdersAPI.getAll()
        if (!data?.success) throw new Error(data?.error || 'Failed to load orders')
        setOrders(data.orders || [])
      } catch (err) {
        setError(err?.message || 'Failed to load orders')
      } finally {
        setLoading(false)
      }
    }
    fetchOrders()
  }, [])

  const statusBadge = (status) => {
    const map = {
      pending: 'bg-yellow-100 text-yellow-800',
      paid: 'bg-blue-100 text-blue-800',
      shipped: 'bg-purple-100 text-purple-800',
      delivered: 'bg-green-100 text-green-800',
      cancelled: 'bg-red-100 text-red-800'
    }
    return map[status] || 'bg-gray-100 text-gray-800'
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="max-w-6xl mx-auto px-6 lg:px-10 py-10">
        <div className="bg-white border border-gray-200 rounded-2xl p-6 md:p-8 shadow-sm mb-8">
          <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
              <h1 className="text-3xl md:text-4xl font-bold text-gray-900">My Orders</h1>
              <p className="text-gray-600 mt-2">Track your purchases and their delivery status.</p>
            </div>
            <div className="flex flex-wrap items-center justify-center gap-4">
              <div className="px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-center min-w-[110px]">
                <div className="text-xs text-gray-500">Total</div>
                <div className="text-xl font-semibold text-gray-900">{summary.total}</div>
              </div>
              <div className="px-4 py-3 rounded-xl bg-green-50 border border-green-100 text-center min-w-[110px]">
                <div className="text-xs text-green-700">Delivered</div>
                <div className="text-xl font-semibold text-green-700">{summary.delivered}</div>
              </div>
              <div className="px-4 py-3 rounded-xl bg-yellow-50 border border-yellow-100 text-center min-w-[110px]">
                <div className="text-xs text-yellow-700">Pending</div>
                <div className="text-xl font-semibold text-yellow-700">{summary.pending}</div>
              </div>
            </div>
          </div>
        </div>

        {loading && <p className="text-gray-600">Loading orders...</p>}
        {error && <p className="text-red-600">{error}</p>}

        {!loading && !error && orders.length === 0 && (
          <div className="bg-white rounded-2xl p-8 border border-gray-200 text-center">
            <div className="text-gray-900 font-semibold mb-2">No orders yet</div>
            <p className="text-gray-600">Start shopping to see your orders here.</p>
          </div>
        )}

        {orders.length > 0 && (
          <div className="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead className="bg-gray-50">
                  <tr>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {orders.map((order) => {
                    const id = order.order_number || `ORD-${order.id}`
                    const itemsCount = Array.isArray(order.items) ? order.items.length : order.items_count || 0
                    const amount = Number(order.total_amount ?? order.amount ?? order.total ?? 0) || 0
                    const paymentUrl = order.payment_url || order.paymentUrl || ''
                    return (
                      <tr key={order.id} className="hover:bg-gray-50">
                        <td className="px-6 py-4 text-sm font-medium text-gray-900">{id}</td>
                        <td className="px-6 py-4 text-sm text-gray-600">{itemsCount}</td>
                        <td className="px-6 py-4 text-sm font-semibold text-gray-900">${amount.toFixed(2)}</td>
                        <td className="px-6 py-4 text-sm">
                          <span className={`px-3 py-1 rounded-full text-xs font-medium ${statusBadge(order.status)}`}>
                            {order.status || 'pending'}
                          </span>
                        </td>
                        <td className="px-6 py-4 text-sm text-gray-600">{order.created_at || order.date || ''}</td>
                        <td className="px-6 py-4 text-sm">
                          {paymentUrl ? (
                            <a
                              href={paymentUrl}
                              target="_blank"
                              rel="noreferrer"
                              className="inline-flex items-center gap-2 text-teal-700 font-semibold hover:text-teal-800"
                            >
                              Pay now
                              <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14 5l7 7-7 7M3 12h18" />
                              </svg>
                            </a>
                          ) : (
                            <span className="text-xs text-gray-400">N/A</span>
                          )}
                        </td>
                      </tr>
                    )
                  })}
                </tbody>
              </table>
            </div>
          </div>
        )}
      </div>
    </div>
  )
}
