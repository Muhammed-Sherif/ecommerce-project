import React, { useState, useEffect, useRef } from 'react'
import { Link, useNavigate, useLocation } from 'react-router-dom'
import { useCart } from '../contexts/CartContext'
import { AuthAPI } from '../api'
import { clearAuth, getAccessToken } from '../auth'

export default function Header() {
    const { getCartCount } = useCart()
    const navigate = useNavigate()
    const location = useLocation()

    const [isSearchOpen, setIsSearchOpen] = useState(false)
    const [searchQuery, setSearchQuery] = useState('')
    const [isUserMenuOpen, setIsUserMenuOpen] = useState(false)
    const userMenuRef = useRef(null)

    const user = JSON.parse(localStorage.getItem('user') || 'null')
    const isLoggedIn = !!getAccessToken()

    useEffect(() => {
        const handleClickOutside = (event) => {
            if (userMenuRef.current && !userMenuRef.current.contains(event.target)) {
                setIsUserMenuOpen(false)
            }
        }
        if (isUserMenuOpen) {
            document.addEventListener('mousedown', handleClickOutside)
        }
        return () => document.removeEventListener('mousedown', handleClickOutside)
    }, [isUserMenuOpen])

    const handleLogout = async () => {
        try {
            await AuthAPI.logout()
        } catch {
            // ignore
        } finally {
            clearAuth()
            setIsUserMenuOpen(false)
            navigate('/login')
        }
    }

    const handleSearch = (e) => {
        e.preventDefault()
        if (searchQuery.trim()) {
            navigate(`/?search=${encodeURIComponent(searchQuery.trim())}`)
            setIsSearchOpen(false)
            setSearchQuery('')
        }
    }

    return (
        <header className="border-b border-gray-200 sticky top-0 bg-white/90 backdrop-blur-md z-50">
            <div className="max-w-7xl mx-auto px-6 lg:px-10 py-4 flex items-center justify-between">
                <Link to="/" className="text-xl font-bold text-gray-900 tracking-tight">AR-FURNITURE</Link>

                <nav className="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                    <Link to="/" className="hover:text-teal-600 transition">HOME</Link>
                    <a href="/#products" className="hover:text-teal-600 transition">PRODUCTS</a>
                    <a href="/#about" className="hover:text-teal-600 transition">ABOUT</a>
                    <a href="/#services" className="hover:text-teal-600 transition">SERVICES</a>
                </nav>

                <div className="flex items-center gap-4">
                    <div className="relative">
                        {isSearchOpen ? (
                            <form onSubmit={handleSearch} className="flex items-center border border-gray-300 rounded-full px-3 py-1 animate-fadeIn">
                                <input
                                    type="text"
                                    placeholder="Search..."
                                    className="outline-none text-sm bg-transparent w-32 sm:w-48 text-gray-700"
                                    value={searchQuery}
                                    onChange={(e) => setSearchQuery(e.target.value)}
                                    autoFocus
                                />
                                <button type="button" onClick={() => { setIsSearchOpen(false); setSearchQuery(''); }} className="ml-2 text-gray-400 hover:text-gray-600">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </form>
                        ) : (
                            <button
                                className="p-2 hover:bg-gray-100 rounded-full transition"
                                onClick={() => setIsSearchOpen(true)}
                            >
                                <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        )}
                    </div>

                    <Link to="/cart" className="p-2 relative hover:bg-gray-100 rounded-full transition">
                        <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        {getCartCount() > 0 && (
                            <span className="absolute -top-1 -right-1 bg-teal-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center animate-bounce">
                                {getCartCount()}
                            </span>
                        )}
                    </Link>

                    <Link to="/orders" className="p-2 hover:bg-gray-100 rounded-full transition" aria-label="Orders">
                        <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 17v-2a2 2 0 012-2h2a2 2 0 012 2v2m-8 0a2 2 0 002 2h4a2 2 0 002-2m-8 0H7a2 2 0 01-2-2V7a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-1" />
                        </svg>
                    </Link>

                    <div className="relative" ref={userMenuRef}>
                        {isLoggedIn ? (
                            <>
                                <button
                                    type="button"
                                    onClick={() => setIsUserMenuOpen((open) => !open)}
                                    className="p-2 hover:bg-gray-100 rounded-full transition"
                                    aria-label="Account menu"
                                >
                                    <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </button>
                                {isUserMenuOpen && (
                                    <div className="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden z-50">
                                        <div className="px-4 py-3 border-b border-gray-100">
                                            <div className="text-sm font-semibold text-gray-900">
                                                {user?.name || user?.email || 'Account'}
                                            </div>
                                            {user?.email && (
                                                <div className="text-xs text-gray-500">{user.email}</div>
                                            )}
                                        </div>
                                        <Link
                                            to="/profile"
                                            onClick={() => setIsUserMenuOpen(false)}
                                            className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                        >
                                            Edit Profile
                                        </Link>
                                        <button
                                            type="button"
                                            onClick={handleLogout}
                                            className="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
                                        >
                                            Logout
                                        </button>
                                    </div>
                                )}
                            </>
                        ) : (
                            <Link to="/login" className="p-2 hover:bg-gray-100 rounded-full transition">
                                <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </Link>
                        )}
                    </div>
                </div>
            </div>
        </header>
    )
}
