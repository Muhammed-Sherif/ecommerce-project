import axios from 'axios'
import { getAccessToken, setAccessToken, getRefreshToken, setRefreshToken, clearAuth } from './auth'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000',
  headers: {}
})

api.interceptors.request.use((config) => {
  const token = getAccessToken()
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

let isRefreshing = false
let queue = []

api.interceptors.response.use(
  (res) => res,
  async (error) => {
    // Handle network errors
    if (!error.response) {
      console.error('Network Error:', error.message)
      if (error.code === 'ERR_NETWORK' || error.message.includes('Network Error')) {
        error.message = 'Cannot connect to server. Please make sure the API server is running.'
      }
      return Promise.reject(error)
    }

    const original = error.config
    if (error.response && error.response.status === 401 && !original._retry) {
      original._retry = true
      if (isRefreshing) {
        return new Promise((resolve, reject) => {
          queue.push({ resolve, reject })
        }).then((token) => {
          original.headers.Authorization = `Bearer ${token}`
          return api(original)
        })
      }
      try {
        isRefreshing = true
        const refresh = getRefreshToken()
        if (!refresh) throw new Error('No refresh token')
        const { data } = await axios.post(`${api.defaults.baseURL}/api/refresh`, { refresh_token: refresh })
        if (!data?.access_token) throw new Error('No access token in refresh')
        setAccessToken(data.access_token)
        queue.forEach(p => p.resolve(data.access_token))
        queue = []
        original.headers.Authorization = `Bearer ${data.access_token}`
        return api(original)
      } catch (e) {
        queue.forEach(p => p.reject(e))
        queue = []
        clearAuth()
        return Promise.reject(e)
      } finally {
        isRefreshing = false
      }
    }
    return Promise.reject(error)
  }
)

export default api

export const AuthAPI = {
  login: (email, password) => api.post('/api/auth/login', { email, password }),
  register: (payload) => api.post('/api/auth/register', payload),
  me: () => api.get('/api/auth/me'),
  logout: () => api.post('/api/auth/logout')
}

export const CmsAPI = {
  getSettings: (group) => api.get(`/api/settings${group ? `?group=${group}` : ''}`),
  updateSettings: (settings) => api.post('/api/settings', { settings })
}

export const NewsletterAPI = {
  subscribe: (email) => api.post('/api/newsletter/subscribe', { email })
}

export const CartAPI = {
  getCart: () => api.get('/api/cart'),
  addItem: (payload) => api.post('/api/cart', payload),
  updateItem: (payload) => api.put('/api/cart', payload),
  removeItem: (payload) => api.delete('/api/cart', { data: payload }),
  clear: () => api.delete('/api/cart/clear')
}

export const CommentsAPI = {
  getByProduct: (productId) => api.get(`/api/products/${productId}/comments`),
  create: (payload) => api.post('/api/comments', payload)
}

export const ReviewsAPI = {
  getByProduct: (productId) => api.get(`/api/products/${productId}/reviews`),
  create: (payload) => api.post('/api/reviews', payload)
}

export const RefermentAPI = {
  getAll: () => api.get('/api/referments'),
  getMine: () => api.get('/api/referments/me'),
  create: (payload) => api.post('/api/referments', payload)
}

export const OrdersAPI = {
  getAll: (params) => api.get('/api/orders', { params }),
  getById: (id) => api.get(`/api/orders/${id}`),
  create: (payload) => api.post('/api/orders', payload),
  updateStatus: (id, payload) => api.put(`/api/orders/${id}/status`, payload),
  cancel: (id) => api.post(`/api/orders/${id}/cancel`)
}

export const CheckoutAPI = {
  checkout: (payload) => api.post('/api/checkout', payload)
}

export const ProductsAPI = {
  getAll: () => api.get('/api/products'),
  getAllForDashboard: () => api.get('/api/products/dashboard'),
  getAllActivePublic: () => api.get('/api/products/public'),
  getById: (id) => api.get(`/api/products/${id}`),
  getByIdForDashboard: (id) => api.get(`/api/products/${id}/dashboard`),
  getByIdPublic: (id) => api.get(`/api/products/${id}/public`),
  create: (payload) => {
    return api.post('/api/products', payload)
  },
  update: (id, payload) => {
    if (payload instanceof FormData) {
      if (!payload.has('_method')) {
        payload.append('_method', 'PUT')
      }
      return api.post(`/api/products/${id}`, payload)
    }
    return api.put(`/api/products/${id}`, payload)
  },
  remove: (id) => api.delete(`/api/products/${id}`)
}

export const UsersAPI = {
  getAll: () => api.get('/api/users'),
  getById: (id) => api.get(`/api/users/${id}`),
  update: (id, payload) => api.put(`/api/users/${id}`, payload),
  remove: (id) => api.delete(`/api/users/${id}`)
}

export const InventoryAPI = {
  getByProduct: (productId) => api.get(`/api/inventory/product/${productId}`),
  adjust: (payload) => api.post('/api/inventory/adjust', payload)
}

export const CouponAPI = {
  getByCode: (code) => api.get(`/api/coupons/code/${code}`),
  validateByCode: (code) => api.get(`/api/coupons/validate/${code}`),
  getAll: () => api.get('/api/coupons'),
  create: (payload) => api.post('/api/coupons', payload),
  update: (id, payload) => api.put(`/api/coupons/${id}`, payload),
  remove: (id) => api.delete(`/api/coupons/${id}`)
}

export const ProfileAPI = {
  getShipping: () => api.get('/api/profile/shipping'),
  updateShipping: (payload) => api.put('/api/profile/shipping', payload)
}
