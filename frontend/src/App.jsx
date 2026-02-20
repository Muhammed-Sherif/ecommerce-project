import { lazy , React, Suspense }from 'react'
import { Routes, Route, Navigate } from 'react-router-dom'
import Loading from './components/Loading'

const Landing = lazy(() => import('./pages/Landing'))
const Login = lazy(() => import('./pages/Login'));
const Register = lazy(() => import('./pages/Register'));
const Cart = lazy(() => import('./pages/Cart'));
const ProductDetail = lazy(() => import('./pages/ProductDetail'));
const Profile = lazy(() => import('./pages/Profile'))
const Orders = lazy(() => import('./pages/Orders'))
const AdminLayout = lazy(() => import('./components/AdminLayout'))
const AdminDashboard = lazy(() => import('./pages/admin/AdminDashboard'))
const AdminOrders = lazy(() => import('./pages/admin/AdminOrders'))
const AdminCMS = lazy(() => import('./pages/admin/AdminCMS'))
const AdminProducts = lazy(() => import('./pages/admin/AdminProducts'))
const AdminUsers = lazy(() => import('./pages/admin/AdminUsers'))
const AdminCoupons = lazy(() => import('./pages/admin/AdminCoupons'))
import { CartProvider } from './contexts/CartContext'
import { getAccessToken } from './auth'
import { RefermentAPI } from './api'

const getStoredUser = () => {
  try {
    return JSON.parse(localStorage.getItem('user') || 'null')
  } catch {
    return null
  }
}

const isActiveAccount = (user) => {
  const status = (user?.status || '').toLowerCase()
  return status === '' || status === 'active'
}

function ProtectedRoute({ children }) {
  const token = getAccessToken()
  if (!token) return <Navigate to="/login" replace />
  return children
}

function DashboardRoute({ children }) {
  const token = getAccessToken()
  const user = getStoredUser()
  if (!token) return <Navigate to="/login" replace />
  if (!isActiveAccount(user)) return <Navigate to="/" replace />
  if (!user || !['admin', 'vendor'].includes(user.role)) return <Navigate to="/" replace />
  return children
}

function AdminRoute({ children }) {
  const token = getAccessToken()
  const user = getStoredUser()
  if (!token) return <Navigate to="/login" replace />
  if (!isActiveAccount(user)) return <Navigate to="/" replace />
  if (!user || user.role !== 'admin') return <Navigate to="/" replace />
  return children
}

function Home() {
  const user = JSON.parse(localStorage.getItem('user') || 'null')
  const [referments, setReferments] = React.useState([])
  const [refermentError, setRefermentError] = React.useState('')

  React.useEffect(() => {
    const fetchReferments = async () => {
      try {
        const { data } = await RefermentAPI.getMine()
        if (!data?.success) throw new Error(data?.message || 'Failed to load referments')
        setReferments(data.referments || [])
      } catch (error) {
        setRefermentError(error?.message || 'Failed to load referments')
      }
    }
    if (getAccessToken()) {
      fetchReferments()
    }
  }, [])
  return (
    <div className="min-h-screen bg-gradient-to-br from-amber-50 via-white to-sky-50 dark:from-slate-900 dark:via-slate-950 dark:to-slate-900 text-slate-900 dark:text-slate-100">
      <div className="max-w-5xl mx-auto p-6">
        <main className="py-6">
          <div className="rounded-xl border border-black/10 dark:border-white/10 p-6 bg-white/70 dark:bg-white/5 backdrop-blur-sm shadow-sm">
            <h2 className="text-2xl font-semibold mb-2">Welcome{user ? `, ${user.email}` : ''}</h2>
            <p className="text-gray-600 dark:text-gray-300">This is a minimal frontend for authentication.</p>
          </div>

          <div className="mt-6 rounded-xl border border-black/10 dark:border-white/10 p-6 bg-white/70 dark:bg-white/5 backdrop-blur-sm shadow-sm">
            <h3 className="text-lg font-semibold mb-3">Your Referments</h3>
            {refermentError && (
              <p className="text-sm text-red-600 mb-3">{refermentError}</p>
            )}
            {referments.length === 0 ? (
              <p className="text-sm text-gray-600 dark:text-gray-300">No referments found yet.</p>
            ) : (
              <ul className="space-y-2">
                {referments.map((ref) => (
                  <li key={ref.id} className="flex items-center justify-between text-sm text-gray-700 dark:text-gray-200">
                    <span className="font-medium">{ref.code}</span>
                    <span className="text-gray-500 dark:text-gray-400">${Number(ref.reward || 0).toFixed(2)}</span>
                  </li>
                ))}
              </ul>
            )}
          </div>
        </main>
      </div>
    </div>
  )
}

export default function App() {
  return (
    <CartProvider>
      <Suspense fallback={<Loading />}>
        <Routes>
          <Route path="/" element={<Landing />} />
          <Route path="/product/:id" element={<ProductDetail />} />
          <Route path="/dashboard" element={<DashboardRoute><Home /></DashboardRoute>} />
          <Route path="/profile" element={<ProtectedRoute><Profile /></ProtectedRoute>} />
          <Route path="/orders" element={<ProtectedRoute><Orders /></ProtectedRoute>} />
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
          <Route path="/cart" element={<Cart />} />

          {/* Admin Routes */}
          <Route path="/admin" element={<AdminRoute><AdminLayout /></AdminRoute>}>
            <Route index element={<AdminDashboard />} />
            <Route path="orders" element={<AdminOrders />} />
            <Route path="cms" element={<AdminCMS />} />
            <Route path="products" element={<AdminProducts />} />
            <Route path="users" element={<AdminUsers />} />
            <Route path="coupons" element={<AdminCoupons />} />
            <Route path="profile" element={<Profile />} />
          </Route>

          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </Suspense>
    </CartProvider>
  )
}
