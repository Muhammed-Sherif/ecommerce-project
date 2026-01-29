import React, { useEffect, useState } from 'react'
import { UsersAPI } from '../../api'

export default function AdminUsers() {
  const [users, setUsers] = useState([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState('')
  const [editingId, setEditingId] = useState(null)
  const [saving, setSaving] = useState(false)
  const [editForm, setEditForm] = useState({
    name: '',
    email: '',
    role: 'customer',
    password: ''
  })

  const fetchUsers = async () => {
    setLoading(true)
    setError('')
    try {
      const { data } = await UsersAPI.getAll()
      const list = data?.users || data || []
      setUsers(Array.isArray(list) ? list : [])
    } catch (err) {
      setError(err?.response?.data?.message || err?.message || 'Failed to load users')
      setUsers([])
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    fetchUsers()
  }, [])

  const startEdit = (user) => {
    setEditingId(user.id)
    setEditForm({
      name: user.name || '',
      email: user.email || '',
      role: user.role || 'customer',
      password: ''
    })
  }

  const cancelEdit = () => {
    setEditingId(null)
    setEditForm({ name: '', email: '', role: 'customer', password: '' })
  }

  const handleEditChange = (key, value) => {
    setEditForm((prev) => ({ ...prev, [key]: value }))
  }

  const handleUpdate = async (e) => {
    e.preventDefault()
    if (!editingId) return
    setSaving(true)
    setError('')
    try {
      const payload = {
        name: editForm.name.trim(),
        email: editForm.email.trim(),
        role: editForm.role || 'customer'
      }
      if (editForm.password && editForm.password.trim()) {
        payload.password = editForm.password
      }
      const { data } = await UsersAPI.update(editingId, payload)
      if (!data?.success) {
        throw new Error(data?.message || 'Failed to update user')
      }
      await fetchUsers()
      cancelEdit()
    } catch (err) {
      setError(err?.response?.data?.message || err?.message || 'Failed to update user')
    } finally {
      setSaving(false)
    }
  }

  const handleDelete = async (id) => {
    if (!window.confirm('Delete this user?')) return
    try {
      await UsersAPI.remove(id)
      setUsers((prev) => prev.filter((u) => u.id !== id))
    } catch (err) {
      alert(err?.response?.data?.message || err?.message || 'Failed to delete user')
    }
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Users</h1>
        <p className="text-sm text-gray-600">Manage registered users.</p>
      </div>

      <div className="flex items-center justify-between">
        {error && <p className="text-sm text-red-600">{error}</p>}
        <button
          onClick={fetchUsers}
          className="px-4 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50"
        >
          Refresh
        </button>
      </div>

      {editingId && (
        <div className="bg-white rounded-lg border border-gray-200 p-6">
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-lg font-semibold text-gray-900">Edit User</h2>
            <button
              onClick={cancelEdit}
              className="text-sm text-gray-600 hover:text-gray-900"
            >
              Cancel
            </button>
          </div>
          <form onSubmit={handleUpdate} className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Name</label>
              <input
                type="text"
                value={editForm.name}
                onChange={(e) => handleEditChange('name', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input
                type="email"
                value={editForm.email}
                onChange={(e) => handleEditChange('email', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Role</label>
              <select
                value={editForm.role}
                onChange={(e) => handleEditChange('role', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg"
              >
                <option value="customer">Customer</option>
                <option value="admin">Admin</option>
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Password</label>
              <input
                type="password"
                value={editForm.password}
                onChange={(e) => handleEditChange('password', e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg"
                placeholder="Leave blank to keep current"
              />
            </div>
            <div className="md:col-span-2">
              <button
                type="submit"
                disabled={saving}
                className="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:bg-blue-400"
              >
                {saving ? 'Saving...' : 'Update User'}
              </button>
            </div>
          </form>
        </div>
      )}

      <div className="bg-white rounded-lg border border-gray-200">
        <div className="px-6 py-4 border-b border-gray-200">
          <h2 className="text-lg font-semibold text-gray-900">All Users</h2>
        </div>
        {loading ? (
          <div className="p-6 text-sm text-gray-600">Loading users...</div>
        ) : users.length === 0 ? (
          <div className="p-6 text-sm text-gray-600">No users found.</div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-200">
                {users.map((user) => (
                  <tr key={user.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4 text-sm text-gray-900">{user.id}</td>
                    <td className="px-6 py-4 text-sm text-gray-700">{user.name || '-'}</td>
                    <td className="px-6 py-4 text-sm text-gray-600">{user.email}</td>
                    <td className="px-6 py-4 text-sm text-gray-600">{user.role || 'customer'}</td>
                    <td className="px-6 py-4 text-sm">
                      <div className="flex items-center gap-3">
                        <button
                          onClick={() => startEdit(user)}
                          className="text-blue-600 hover:text-blue-800 font-medium"
                        >
                          Edit
                        </button>
                        <button
                          onClick={() => handleDelete(user.id)}
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
