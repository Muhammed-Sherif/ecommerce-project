import React, { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import { CmsAPI } from '../../api'

export default function AdminCMS() {
    const [settings, setSettings] = useState({})
    const [loading, setLoading] = useState(true)
    const [saving, setSaving] = useState(false)

    useEffect(() => {
        const fetchCurrentSettings = async () => {
            try {
                const { data } = await CmsAPI.getSettings('landing')
                setSettings(data)
            } catch (err) {
                console.error('Failed to fetch CMS settings:', err)
            } finally {
                setLoading(false)
            }
        }
        fetchCurrentSettings()
    }, [])

    const handleChange = (key, value) => {
        setSettings(prev => ({ ...prev, [key]: value }))
    }

    const handleSave = async () => {
        setSaving(true)
        try {
            await CmsAPI.updateSettings(settings)
            alert('Landing page updated successfully!')
        } catch (error) {
            console.error('Failed to update settings:', error)
            alert('Failed to update settings: ' + (error.response?.data?.message || error.message))
        } finally {
            setSaving(false)
        }
    }

    if (loading) return <div className="p-8">Loading CMS...</div>

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h1 className="text-2xl font-bold text-gray-900">Landing Page CMS</h1>
                <button
                    onClick={handleSave}
                    disabled={saving}
                    className="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:bg-indigo-400 transition flex items-center gap-2"
                >
                    {saving ? 'Saving...' : 'Save Changes'}
                </button>
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-8">
                {/* Hero Section */}
                <div>
                    <h2 className="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">Hero Section</h2>
                    <div className="grid gap-6">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Hero Title</label>
                            <input
                                type="text"
                                value={settings['landing.hero_title'] || ''}
                                onChange={(e) => handleChange('landing.hero_title', e.target.value)}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Hero Subtitle</label>
                            <textarea
                                value={settings['landing.hero_subtitle'] || ''}
                                onChange={(e) => handleChange('landing.hero_subtitle', e.target.value)}
                                rows={3}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            />
                        </div>
                        <div className="grid md:grid-cols-2 gap-6">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">CTA Button Text</label>
                                <input
                                    type="text"
                                    value={settings['landing.cta_text'] || ''}
                                    onChange={(e) => handleChange('landing.cta_text', e.target.value)}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-gray-700 mb-1">Hero Image URL</label>
                                <input
                                    type="text"
                                    value={settings['landing.hero_image'] || ''}
                                    onChange={(e) => handleChange('landing.hero_image', e.target.value)}
                                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                />
                            </div>
                        </div>
                        {settings['landing.hero_image'] && (
                            <div className="mt-2">
                                <p className="text-sm text-gray-500 mb-2">Preview:</p>
                                <img
                                    src={settings['landing.hero_image']}
                                    alt="Hero Preview"
                                    className="w-full max-w-md h-48 object-cover rounded-lg border border-gray-200"
                                />
                            </div>
                        )}
                    </div>
                </div>

                {/* About Section */}
                <div>
                    <h2 className="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">About / Services Section</h2>
                    <div className="grid gap-6">
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Section Title</label>
                            <input
                                type="text"
                                value={settings['landing.about_title'] || ''}
                                onChange={(e) => handleChange('landing.about_title', e.target.value)}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-gray-700 mb-1">Description Text</label>
                            <textarea
                                value={settings['landing.about_text'] || ''}
                                onChange={(e) => handleChange('landing.about_text', e.target.value)}
                                rows={3}
                                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )
}
