import React, { useState, useEffect } from 'react'
import { OrdersAPI } from '../../api'
import { alertError, confirm } from '../../utils/alert'

export default function AdminOrders() {
  const [orders, setOrders] = useState([])
  const [filter, setFilter] = useState('all')
  const [searchTerm, setSearchTerm] = useState('')
  const [selectedOrder, setSelectedOrder] = useState(null)
  const [statusUpdate, setStatusUpdate] = useState('')

  useEffect(() => {
    const fetchOrders = async () => {
      try {
        const { data } = await OrdersAPI.getAll()
        if (!data?.success) throw new Error(data?.error || 'Failed to load orders')
        const normalized = (data.orders || []).map((order) => {
          const itemsCount = Array.isArray(order.items) ? order.items.length : order.items_count || 0
          const amount = order.total_amount ?? order.amount ?? order.total ?? 0
          return {
            id: order.order_number || `ORD-${order.id}`,
            rawId: order.id,
            customer: order.customer_name || `Customer #${order.customer_id ?? '-'}`,
            email: order.customer_email || order.email || '',
            items: itemsCount,
            amount: Number(amount) || 0,
            status: order.status || 'pending',
            date: order.created_at || order.date || '',
            raw: order
          }
        })
        setOrders(normalized)
      } catch (error) {
        console.error('Failed to fetch orders:', error)
        setOrders([])
      }
    }

    fetchOrders()
  }, [])

  const getStatusColor = (status) => {
    const colors = {
      pending: 'bg-yellow-100 text-yellow-800',
      paid: 'bg-green-100 text-green-800',
      shipped: 'bg-purple-100 text-purple-800',
      delivered: 'bg-green-100 text-green-800',
      cancelled: 'bg-red-100 text-red-800'
    }
    return colors[status] || 'bg-gray-100 text-gray-800'
  }

  const handleView = (order) => {
    setSelectedOrder(order)
    setStatusUpdate(order.status)
  }

  const handleUpdateStatus = async () => {
    if (!selectedOrder?.rawId) return
    try {
      const { data } = await OrdersAPI.updateStatus(selectedOrder.rawId, { status: statusUpdate })
      if (!data?.success) throw new Error(data?.message || 'Failed to update status')
      setOrders((prev) =>
        prev.map((o) => (o.rawId === selectedOrder.rawId ? { ...o, status: statusUpdate } : o))
      )
      setSelectedOrder((prev) => (prev ? { ...prev, status: statusUpdate } : prev))
    } catch (error) {
      alertError('Failed to update status', error?.response?.data?.message || error?.message)
    }
  }

  const handleDelete = async (order) => {
    if (!order?.rawId) return
    const ok = await confirm({ title: 'Cancel this order?', text: 'This action cannot be undone.' })
    if (!ok) return
    try {
      const { data } = await OrdersAPI.cancel(order.rawId)
      if (!data?.success) throw new Error(data?.message || 'Failed to cancel order')
      setOrders((prev) => prev.filter((o) => o.rawId !== order.rawId))
      if (selectedOrder?.rawId === order.rawId) {
        setSelectedOrder(null)
      }
    } catch (error) {
      alertError('Failed to cancel order', error?.response?.data?.message || error?.message)
    }
  }

  const filteredOrders = orders.filter((order) => {
    const matchesFilter = filter === 'all' || order.status === filter
    const matchesSearch =
      order.id.toLowerCase().includes(searchTerm.toLowerCase()) ||
      order.customer.toLowerCase().includes(searchTerm.toLowerCase())
    return matchesFilter && matchesSearch
  })

  return (
    <div className="space-y-6">
      <div className="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1">
            <input
              type="text"
              placeholder="Search by order ID or customer name..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"
            />
          </div>
          <div className="flex gap-2">
            {['all', 'pending', 'paid', 'shipped', 'delivered', 'cancelled'].map((status) => (
              <button
                key={status}
                onClick={() => setFilter(status)}
                className={`px-4 py-2 rounded-lg font-medium transition ${
                  filter === status
                    ? 'bg-teal-600 text-white'
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                }`}
              >
                {status.charAt(0).toUpperCase() + status.slice(1)}
              </button>
            ))}
          </div>
        </div>
      </div>

      <div className="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {filteredOrders.map((order) => (
                <tr key={order.id} className="hover:bg-gray-50">
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="text-sm font-medium text-gray-900">{order.id}</div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="text-sm font-medium text-gray-900">{order.customer}</div>
                    <div className="text-sm text-gray-500">{order.email}</div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{order.items}</td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                    ${order.amount.toFixed(2)}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className={`px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(order.status)}`}>
                      {order.status}
                    </span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{order.date}</td>
                  <td className="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                    <button onClick={() => handleView(order)} className="text-teal-600 hover:text-teal-800 font-medium">
                      View
                    </button>
                    <button onClick={() => handleView(order)} className="text-blue-600 hover:text-blue-800 font-medium">
                      Edit
                    </button>
                    <button onClick={() => handleDelete(order)} className="text-red-600 hover:text-red-800 font-medium">
                      Delete
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {filteredOrders.length === 0 && (
        <div className="text-center py-12 text-gray-500">No orders found matching your criteria.</div>
      )}

      {selectedOrder && (
        <div className="fixed inset-0 bg-black/30 flex items-center justify-center p-4">
          <div className="bg-white rounded-lg shadow-lg max-w-lg w-full p-6">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-lg font-semibold text-gray-900">Order Details</h3>
              <button onClick={() => setSelectedOrder(null)} className="text-gray-500 hover:text-gray-800">
                ?
              </button>
            </div>
            <div className="space-y-2 text-sm text-gray-700">
              <div>
                <span className="font-medium">Order ID:</span> {selectedOrder.id}
              </div>
              <div>
                <span className="font-medium">Customer:</span> {selectedOrder.customer}
              </div>
              <div>
                <span className="font-medium">Email:</span> {selectedOrder.email || '-'}
              </div>
              <div>
                <span className="font-medium">Items:</span> {selectedOrder.items}
              </div>
              <div>
                <span className="font-medium">Amount:</span> ${selectedOrder.amount.toFixed(2)}
              </div>
              <div>
                <span className="font-medium">Status:</span> {selectedOrder.status}
              </div>
            </div>
            <div className="mt-4">
              <label className="block text-sm font-medium text-gray-700 mb-1">Update Status</label>
              <div className="flex items-center gap-3">
                <select
                  value={statusUpdate}
                  onChange={(e) => setStatusUpdate(e.target.value)}
                  className="px-3 py-2 border border-gray-300 rounded-lg"
                >
                  {['pending', 'paid', 'shipped', 'delivered', 'cancelled'].map((s) => (
                    <option key={s} value={s}>
                      {s}
                    </option>
                  ))}
                </select>
                <button onClick={handleUpdateStatus} className="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700">
                  Save
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  )
}
