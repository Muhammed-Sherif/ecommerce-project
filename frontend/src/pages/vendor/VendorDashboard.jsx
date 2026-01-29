import React, { useState, useEffect } from 'react'

export default function VendorDashboard() {
    const [stats, setStats] = useState({
        totalProducts: 0,
        totalSales: 0,
        pendingOrders: 0,
        revenue: 0
    })

    useEffect(() => {
        setStats({
            totalProducts: 24,
            totalSales: 156,
            pendingOrders: 8,
            revenue: 12450.00
        })
    }, [])

    const StatCard = ({ title, value, icon, color }) => (
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
        </div>
    )

    return (
        <div className="space-y-8">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <StatCard
                    title="Total Products"
                    value={stats.totalProducts}
                    icon="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                    color="bg-purple-600"
                />
                <StatCard
                    title="Total Sales"
                    value={stats.totalSales}
                    icon="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                    color="bg-blue-600"
                />
                <StatCard
                    title="Pending Orders"
                    value={stats.pendingOrders}
                    icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                    color="bg-yellow-600"
                />
                <StatCard
                    title="Revenue"
                    value={`$${stats.revenue.toLocaleString()}`}
                    icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    color="bg-green-600"
                />
            </div>

            <div className="bg-white rounded-lg shadow-sm border border-gray-100 p-8 text-center">
                <h3 className="text-xl font-semibold text-gray-900 mb-4">Welcome to Your Vendor Dashboard</h3>
                <p className="text-gray-600 mb-6">Manage your products, track orders, and monitor your sales performance.</p>
                <div className="flex justify-center gap-4">
                    <button className="px-6 py-3 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition">
                        Add New Product
                    </button>
                    <button className="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                        View All Orders
                    </button>
                </div>
            </div>
        </div>
    )
}
