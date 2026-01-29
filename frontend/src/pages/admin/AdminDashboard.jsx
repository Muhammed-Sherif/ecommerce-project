import React, { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import { OrdersAPI, ProductsAPI, UsersAPI } from '../../api'

export default function AdminDashboard() {
    const navigate = useNavigate()
    const [stats, setStats] = useState({
        totalOrders: 0,
        totalRevenue: 0,
        totalProducts: 0,
        totalUsers: 0,
        pendingOrders: 0,
        lowStockProducts: 0
    })

    const [recentOrders, setRecentOrders] = useState([])

    useEffect(() => {
        const fetchOrders = async () => {
            try {
                const { data } = await OrdersAPI.getAll({ per_page: 5 })
                if (!data?.success) throw new Error(data?.error || 'Failed to load orders')
                const list = data.orders || []
                const normalized = list.map((order) => ({
                    id: order.order_number || `ORD-${order.id}`,
                    customer: order.customer_name || `Customer #${order.customer_id ?? '—'}`,
                    amount: Number(order.total_amount ?? order.amount ?? order.total ?? 0) || 0,
                    status: order.status || 'pending',
                    date: order.created_at || order.date || ''
                }))
                setRecentOrders(normalized)

                const totalRevenue = list.reduce((sum, o) => {
                    const amount = Number(o.total_amount ?? o.amount ?? o.total ?? 0) || 0
                    return sum + amount
                }, 0)

                setStats((prev) => ({
                    ...prev,
                    totalOrders: list.length,
                    totalRevenue,
                    pendingOrders: list.filter((o) => o.status === 'pending').length
                }))
            } catch (error) {
                console.error('Failed to fetch orders:', error)
                setRecentOrders([])
            }
        }

        fetchOrders()
    }, [])

    useEffect(() => {
        const fetchMeta = async () => {
            try {
                const [productsRes, usersRes] = await Promise.all([
                    ProductsAPI.getAll(),
                    UsersAPI.getAll()
                ])
                const products = productsRes?.data?.products || productsRes?.data || []
                const users = usersRes?.data?.users || usersRes?.data || []
                const lowStockProducts = products.filter((p) => Number(p.stock ?? 0) <= 5).length

                setStats((prev) => ({
                    ...prev,
                    totalProducts: products.length,
                    lowStockProducts,
                    totalUsers: users.length
                }))
            } catch (error) {
                console.error('Failed to fetch admin stats:', error)
            }
        }

        fetchMeta()
    }, [])

    const StatCard = ({ title, value, icon, color, subtitle }) => (
        <div className="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
            <div className="flex items-center justify-between mb-4">
                <div className={`p-3 rounded-lg ${color}`}>
                    <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={icon} />
                    </svg>
                </div>
            </div>
            <h3 className="text-2xl font-bold text-gray-900 mb-1">{value}</h3>
            <p className="text-sm text-gray-600">{title}</p>
            {subtitle && <p className="text-xs text-gray-500 mt-2">{subtitle}</p>}
        </div>
    )

    const getStatusColor = (status) => {
        const colors = {
            pending: 'bg-yellow-100 text-yellow-800',
            confirmed: 'bg-blue-100 text-blue-800',
            shipped: 'bg-purple-100 text-purple-800',
            delivered: 'bg-green-100 text-green-800',
            cancelled: 'bg-red-100 text-red-800'
        }
        return colors[status] || 'bg-gray-100 text-gray-800'
    }

    return (
        <div className="space-y-8">
            {/* Stats Grid */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <StatCard
                    title="Total Orders"
                    value={stats.totalOrders.toLocaleString()}
                    icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                    color="bg-blue-600"
                    subtitle={`${stats.pendingOrders} pending orders`}
                />
                <StatCard
                    title="Total Revenue"
                    value={`$${stats.totalRevenue.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`}
                    icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    color="bg-green-600"
                />
                <StatCard
                    title="Total Products"
                    value={stats.totalProducts}
                    icon="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                    color="bg-purple-600"
                    subtitle={`${stats.lowStockProducts} low stock items`}
                />
                <StatCard
                    title="Total Users"
                    value={stats.totalUsers.toLocaleString()}
                    icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                    color="bg-teal-600"
                />
                <StatCard
                    title="Pending Orders"
                    value={stats.pendingOrders}
                    icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                    color="bg-yellow-600"
                />
                <StatCard
                    title="Low Stock Alert"
                    value={stats.lowStockProducts}
                    icon="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                    color="bg-red-600"
                />
            </div>

            {/* Recent Orders */}
            <div className="bg-white rounded-lg shadow-sm border border-gray-100">
                <div className="p-6 border-b border-gray-200">
                    <h3 className="text-lg font-semibold text-gray-900">Recent Orders</h3>
                </div>
                <div className="overflow-x-auto">
                    <table className="w-full">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="bg-white divide-y divide-gray-200">
                            {recentOrders.map((order) => (
                                <tr key={order.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{order.id}</td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{order.customer}</td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">${order.amount.toFixed(2)}</td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <span className={`px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(order.status)}`}>
                                            {order.status}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{order.date}</td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm">
                                        <button className="text-teal-600 hover:text-teal-800 font-medium">View</button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Quick Actions */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <button
                    onClick={() => navigate('/admin/products')}
                    className="bg-white rounded-lg shadow-sm border border-gray-100 p-6 text-left hover:shadow-md transition group"
                >
                    <div className="flex items-center gap-4">
                        <div className="p-3 bg-teal-100 rounded-lg group-hover:bg-teal-200 transition">
                            <svg className="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <div>
                            <h4 className="font-semibold text-gray-900">Add New Product</h4>
                            <p className="text-sm text-gray-600">Create a new product listing</p>
                        </div>
                    </div>
                </button>

                <button
                    onClick={() => navigate('/admin/orders')}
                    className="bg-white rounded-lg shadow-sm border border-gray-100 p-6 text-left hover:shadow-md transition group"
                >
                    <div className="flex items-center gap-4">
                        <div className="p-3 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition">
                            <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h4 className="font-semibold text-gray-900">Process Orders</h4>
                            <p className="text-sm text-gray-600">Review pending orders</p>
                        </div>
                    </div>
                </button>

                <button
                    onClick={() => navigate('/admin/inventory')}
                    className="bg-white rounded-lg shadow-sm border border-gray-100 p-6 text-left hover:shadow-md transition group"
                >
                    <div className="flex items-center gap-4">
                        <div className="p-3 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition">
                            <svg className="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                        </div>
                        <div>
                            <h4 className="font-semibold text-gray-900">Update Inventory</h4>
                            <p className="text-sm text-gray-600">Manage stock levels</p>
                        </div>
                    </div>
                </button>
            </div>
        </div>
    )
}
