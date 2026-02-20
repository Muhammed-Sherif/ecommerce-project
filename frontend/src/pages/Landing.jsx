import React, { useState, useEffect, useRef } from 'react'
import { Link, useNavigate, useLocation } from 'react-router-dom'
import Header from '../components/Header'
import { useCart } from '../contexts/CartContext'
import { CmsAPI, AuthAPI, ProductsAPI, NewsletterAPI } from '../api'
import { clearAuth, getAccessToken } from '../auth'

const services = [
  {
    title: 'Premier Hardwoods',
    description: 'We prefer sustainable materials, ensuring both quality and environmental responsibility.',
    icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z'
  },
  {
    title: 'Excellent Services',
    description: 'Our customer support is dedicated to providing the best shopping experience.',
    icon: 'M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5'
  },
  {
    title: 'Unbeatable Shipping',
    description: 'Fast and reliable shipping to ensure your furniture arrives safely and on time.',
    icon: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4'
  },
  {
    title: 'Unique Style',
    description: 'Our designs blend modern aesthetics with timeless craftsmanship.',
    icon: 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'
  },
]

const useTypewriter = (text, speed = 30, startDelay = 0) => {
  const [value, setValue] = useState('')

  useEffect(() => {
    let cancelled = false
    const timeouts = []
    setValue('')

    const startTyping = () => {
      if (!text) return
      let index = 0
      const tick = () => {
        if (cancelled) return
        setValue(text.slice(0, index + 1))
        index += 1
        if (index < text.length) {
          timeouts.push(setTimeout(tick, speed))
        }
      }
      timeouts.push(setTimeout(tick, speed))
    }

    timeouts.push(setTimeout(startTyping, startDelay))

    return () => {
      cancelled = true
      timeouts.forEach((t) => clearTimeout(t))
    }
  }, [text, speed, startDelay])

  return value
}

export default function Landing() {
  const [activeCategory, setActiveCategory] = useState('All Product')
  const [searchQuery, setSearchQuery] = useState('')
  const [isSearchOpen, setIsSearchOpen] = useState(false)
  const [products, setProducts] = useState([])
  const [filteredProducts, setFilteredProducts] = useState([])
  const [categories, setCategories] = useState(['All Product'])
  const [settings, setSettings] = useState({})
  const [settingsLoaded, setSettingsLoaded] = useState(false)
  const [isUserMenuOpen, setIsUserMenuOpen] = useState(false)
  const userMenuRef = useRef(null)
  const [newsletterEmail, setNewsletterEmail] = useState('')
  const [newsletterStatus, setNewsletterStatus] = useState('')
  const [newsletterError, setNewsletterError] = useState('')
  const [newsletterLoading, setNewsletterLoading] = useState(false)
  const [loadingProductId, setLoadingProductId] = useState(null)

  const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000'

  const resolveImageUrl = (url) => {
    if (!url) return ''
    if (url.startsWith('http://') || url.startsWith('https://') || url.startsWith('data:') || url.startsWith('blob:')) {
      return url
    }
    const base = apiBaseUrl.replace(/\/$/, '')
    const path = url.startsWith('/') ? url : `/${url}`
    return `${base}${path}`
  }

  const { addToCart, getCartCount } = useCart()
  const navigate = useNavigate()

  const user = JSON.parse(localStorage.getItem('user') || 'null')
  const isLoggedIn = !!getAccessToken()

  useEffect(() => {
    // 1. Fetch CMS Settings
    const fetchSettings = async () => {
      try {
        const [landingRes, productsRes] = await Promise.all([
          CmsAPI.getSettings('landing'),
          CmsAPI.getSettings('products')
        ])
        const merged = { ...(landingRes?.data || {}), ...(productsRes?.data || {}) }
        setSettings(merged)

        const rawCategories = merged['products.categories']
        let parsed = []
        if (Array.isArray(rawCategories)) {
          parsed = rawCategories
        } else if (typeof rawCategories === 'string' && rawCategories.trim() !== '') {
          try {
            const json = JSON.parse(rawCategories)
            parsed = Array.isArray(json) ? json : rawCategories.split(',')
          } catch {
            parsed = rawCategories.split(',')
          }
        }
        const normalized = Array.from(
          new Set(
            parsed
              .map((c) => String(c).trim())
              .filter((c) => c.length > 0)
          )
        )
        const nextCategories = ['All Product', ...(normalized.length ? normalized : defaultCategories)]
        setCategories(nextCategories)
        if (!nextCategories.includes(activeCategory)) {
          setActiveCategory('All Product')
        }
      } catch (err) {
        console.error('Failed to fetch CMS settings:', err)
      } finally {
        setSettingsLoaded(true)
      }
    }

    fetchSettings()

    // 2. Fetch Products (Public - only active products)
    const fetchProducts = async () => {
      try {
        const { data } = await ProductsAPI.getAllActivePublic()
        const list = data?.products || data || []
        const normalized = Array.isArray(list)
          ? list.map((product) => ({
              ...product,
              image: resolveImageUrl(
                product.image || (Array.isArray(product.images) ? product.images[0] : null)
              )
            }))
          : []
        setProducts(normalized)
        setFilteredProducts(normalized)
      } catch (err) {
        console.error(err)
      }
    }
    fetchProducts()
  }, [])

  useEffect(() => {
    let result = products

    // Filter by category
    if (activeCategory !== 'All Product') {
      result = result.filter(product => product.category === activeCategory)
    }

    // Filter by search query
    if (searchQuery) {
      const query = searchQuery.toLowerCase()
      result = result.filter(product =>
        product.name.toLowerCase().includes(query) ||
        product.description.toLowerCase().includes(query)
      )
    }

    setFilteredProducts(result)
  }, [activeCategory, searchQuery, products])

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

  const location = useLocation()

  useEffect(() => {
    const params = new URLSearchParams(location.search)
    const search = params.get('search')
    if (search) {
      setSearchQuery(search)
      // Scroll to products section if searching
      const productsSection = document.getElementById('products')
      if (productsSection) {
        productsSection.scrollIntoView({ behavior: 'smooth' })
      }
    }
  }, [location.search])

  useEffect(() => {
    if (!location.hash) return
    const targetId = location.hash.replace('#', '')
    const target = document.getElementById(targetId)
    if (target) {
      target.scrollIntoView({ behavior: 'smooth' })
    }
  }, [location.hash])

  const heroTitle = settingsLoaded ? (settings['landing.hero_title'] ) : ''
  const heroSubtitle = settingsLoaded ? (settings['landing.hero_subtitle'] ) : ''
  // Even slower typing speeds and longer subtitle start delay
  const typedHeroTitle = useTypewriter(heroTitle, 100)
  const typedHeroSubtitle = useTypewriter(
    heroSubtitle,
    60,
    Math.min((heroTitle || '').length * 100 + 800, 5000)
  )
  const isTitleTyping = typedHeroTitle.length < (heroTitle || '').length
  const isSubtitleTyping = typedHeroSubtitle.length < (heroSubtitle || '').length

  const handleNewsletterSubmit = async (e) => {
    e.preventDefault()
    const email = newsletterEmail.trim()
    setNewsletterError('')
    setNewsletterStatus('')
    if (!email) {
      setNewsletterError('Please enter your email address.')
      return
    }
    setNewsletterLoading(true)
    try {
      const { data } = await NewsletterAPI.subscribe(email)
      if (!data?.success) {
        throw new Error(data?.message || 'Subscription failed.')
      }
      setNewsletterStatus(data?.message || 'Thanks for subscribing!')
      setNewsletterEmail('')
    } catch (err) {
      setNewsletterError(err?.response?.data?.message || err?.message || 'Subscription failed.')
    } finally {
      setNewsletterLoading(false)
    }
  }

  return (
    <div className="min-h-screen bg-white">
      <Header />

      {/* Hero Section */}
      <section id="home" className="relative bg-gradient-to-br from-teal-50 to-white overflow-hidden">
        <div className="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-teal-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
        <div className="absolute top-0 left-0 -ml-20 -mt-20 w-96 h-96 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
        <div className="absolute -bottom-32 left-20 w-96 h-96 bg-pink-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000"></div>

        <div className="max-w-7xl mx-auto px-6 lg:px-10 py-24 lg:py-32 grid lg:grid-cols-2 gap-12 items-center relative z-10">
          <div className="space-y-8">
            <h1 className="text-5xl lg:text-7xl font-bold text-gray-900 leading-tight">
              {typeof heroTitle === 'string' ? (typedHeroTitle || '') : ''}
              {isTitleTyping && <span className="inline-block w-2 h-8 lg:h-10 bg-teal-600 ml-2 align-middle animate-pulse" aria-hidden="true"></span>}
            </h1>
            <p className="text-xl text-gray-600 max-w-lg leading-relaxed">
              {typeof heroSubtitle === 'string' ? (typedHeroSubtitle || '') : ''}
              {isSubtitleTyping && <span className="inline-block w-1.5 h-5 bg-gray-400 ml-2 align-middle animate-pulse" aria-hidden="true"></span>}
            </p>

            <div className="flex flex-col sm:flex-row gap-4 pt-4">
              <a href="#products" className="px-8 py-4 bg-teal-600 text-white font-semibold rounded-lg shadow-lg shadow-teal-600/30 hover:bg-teal-700 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 text-center">
                {settings['landing.cta_text'] || 'Shop Now'}
              </a>
              <button className="px-8 py-4 bg-white text-gray-700 font-semibold rounded-lg shadow-md border border-gray-100 hover:bg-gray-50 hover:border-gray-300 transition-all duration-300">
                View Collection
              </button>
            </div>

            <div className="pt-8 flex items-center gap-8 text-gray-500">
              <div className="flex -space-x-4">
                {[1, 2, 3, 4].map((i) => (
                  <div key={i} className="w-10 h-10 rounded-full border-2 border-white bg-gray-200 overflow-hidden">
                    <img src={`https://i.pravatar.cc/100?img=${i + 10}`} alt="User" />
                  </div>
                ))}
              </div>
              <div className="text-sm">
                <span className="font-bold text-gray-900">5k+</span> Happy Customers
              </div>
            </div>
          </div>

          <div className="relative group">
            <div className="relative z-10 rounded-2xl overflow-hidden shadow-2xl transition-transform duration-500 group-hover:scale-[1.02]">
              <img
                src={settings['landing.hero_image'] || "https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=1200&q=80"}
                alt="Modern furniture"
                className="w-full h-full object-cover"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>

              <div className="absolute bottom-6 left-6 right-6">
                <div className="bg-white/90 backdrop-blur p-4 rounded-xl shadow-lg flex items-center justify-between">
                  <div>
                    <h3 className="font-bold text-gray-900">Modern Lounge Chair</h3>
                    <p className="text-sm text-gray-500">Premium Comfort Collection</p>
                  </div>
                  <span className="font-bold text-teal-600">$450</span>
                </div>
              </div>
            </div>

            {/* Decimal Grid Pattern */}
            <div className="absolute -top-4 -right-4 w-24 h-24 bg-dots-pattern opacity-20"></div>
          </div>
        </div>
      </section>

      {/* Services Section */}
      <section id="services" className="py-24 bg-white relative">
        <div className="max-w-7xl mx-auto px-6 lg:px-10">
          <div id="about" className="text-center max-w-3xl mx-auto mb-16">
            <h2 className="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
              {settings['landing.about_title'] || 'Why Choose Us?'}
            </h2>
            <p className="text-gray-600 text-lg">
              {settings['landing.about_text'] || 'We provide an exceptional experience from browsing to delivery, ensuring you get the best value and quality.'}
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            {services.map((service, idx) => (
              <div key={idx} className="bg-gray-50 rounded-2xl p-8 transition-all duration-300 hover:bg-white hover:shadow-xl border border-gray-100 group">
                <div className="w-14 h-14 bg-teal-100 rounded-xl flex items-center justify-center mb-6 text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition-colors duration-300">
                  <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d={service.icon} />
                  </svg>
                </div>
                <h3 className="text-xl font-bold text-gray-900 mb-3">{service.title}</h3>
                <p className="text-gray-600 leading-relaxed">{service.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Products Section */}
      <section id="products" className="py-24 bg-gray-50">
        <div className="max-w-7xl mx-auto px-6 lg:px-10">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold text-gray-900 mb-4">Our Products</h2>
            <p className="text-gray-600 max-w-2xl mx-auto text-lg">
              {settings['landing.products_blurb'] || 'Discover our curated collection of premium furniture pieces crafted for comfort and style.'}
            </p>
          </div>

          {/* Filter Tabs */}
          <div className="flex flex-wrap justify-center gap-3 mb-12">
            {categories.map((cat) => (
              <button
                key={cat}
                onClick={() => setActiveCategory(cat)}
                className={`px-6 py-2.5 rounded-full text-sm font-medium transition-all duration-300 ${activeCategory === cat
                  ? 'bg-gray-900 text-white shadow-lg scale-105'
                  : 'bg-white text-gray-600 hover:bg-gray-200 shadow-sm border border-gray-200'
                  }`}
              >
                {cat}
              </button>
            ))}
          </div>

          {/* Products Grid */}
          {filteredProducts.length > 0 ? (
            <div className="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
              {filteredProducts.map((product) => (
                <div key={product.id} className="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 group product-card">
                  <Link to={`/product/${product.id}`} className="block relative">
                    <div className="bg-gray-100 aspect-[4/3] overflow-hidden relative">
                      <img
                        src={resolveImageUrl(product.image)}
                        alt={product.name}
                        className="w-full h-full object-cover transform group-hover:scale-110 transition duration-700 ease-in-out"
                      />
                      <div className="absolute inset-x-0 bottom-0 p-4 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex justify-end">
                        <span className="bg-white/90 backdrop-blur text-gray-900 text-xs font-bold px-2 py-1 rounded">
                          Quick View
                        </span>
                      </div>
                    </div>
                  </Link>
                  <div className="p-5">
                    <div className="text-xs font-semibold text-teal-600 mb-1 uppercase tracking-wider">{product.category}</div>
                    <Link to={`/product/${product.id}`}>
                      <h3 className="text-lg font-bold text-gray-900 mb-2 hover:text-teal-600 transition-colors">{product.name}</h3>
                    </Link>
                    <p className="text-sm text-gray-500 line-clamp-2 mb-4">{product.description}</p>
                    <div className="flex items-center justify-between pt-2 border-t border-gray-100">
                      <span className="text-xl font-bold text-gray-900">${Number(product.price).toFixed(2)}</span>
                      <button
                        onClick={async (e) => {
                          e.preventDefault()
                          setLoadingProductId(product.id)
                          try {
                            await addToCart(product)
                          } finally {
                            setLoadingProductId(null)
                          }
                        }}
                        disabled={loadingProductId === product.id}
                        className="p-2 bg-gray-100 text-gray-600 rounded-full hover:bg-teal-600 hover:text-white transition-colors duration-300 disabled:opacity-60 disabled:cursor-not-allowed"
                        title="Add to Cart"
                      >
                        {loadingProductId === product.id ? (
                          <svg className="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                        ) : (
                          <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                          </svg>
                        )}
                      </button>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="text-center py-20">
              <div className="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                <svg className="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 className="text-lg font-medium text-gray-900">No products found</h3>
              <p className="text-gray-500 max-w-sm mx-auto mt-2">Try adjusting your search or category filter to find what you're looking for.</p>
              <button
                onClick={() => { setActiveCategory('All Product'); setSearchQuery(''); }}
                className="mt-6 px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition"
              >
                Reset Filters
              </button>
            </div>
          )}
        </div>
      </section>

      {/* Brands Section */}
      <section className="py-16 bg-white border-t border-gray-100">
        <div className="max-w-7xl mx-auto px-6 lg:px-10">
          <p className="text-center text-sm font-semibold text-gray-400 uppercase tracking-widest mb-8">Trusted by renowned companies</p>
          <div className="flex flex-wrap items-center justify-center gap-12 lg:gap-20 opacity-40 grayscale hover:grayscale-0 transition-all duration-500">
            <span className="text-2xl font-bold font-serif hover:text-gray-900 cursor-default transition">VOGUE</span>
            <span className="text-2xl font-bold font-sans hover:text-teal-600 cursor-default transition">Forbes</span>
            <span className="text-2xl font-extrabold font-mono hover:text-gray-900 cursor-default transition">AD</span>
            <span className="text-2xl font-bold italic hover:text-orange-500 cursor-default transition">dwell</span>
            <span className="text-2xl font-black font-sans hover:text-gray-900 cursor-default transition">HOUZZ</span>
          </div>
        </div>
      </section>

      {/* Newsletter Section */}
      <section className="py-20 bg-gray-900 text-white relative overflow-hidden">
        <div className="absolute inset-0 bg-pattern opacity-5"></div>
        <div className="max-w-4xl mx-auto px-6 relative z-10 text-center">
          <h2 className="text-3xl lg:text-4xl font-bold mb-6">Join Our Newsletter</h2>
          <p className="text-gray-400 text-lg mb-8 max-w-2xl mx-auto">
            Subscribe to get special offers, free giveaways, and once-in-a-lifetime deals.
          </p>
          <form className="flex flex-col sm:flex-row gap-4 max-w-md mx-auto" onSubmit={handleNewsletterSubmit}>
            <input
              type="email"
              placeholder="Enter your email"
              value={newsletterEmail}
              onChange={(e) => setNewsletterEmail(e.target.value)}
              required
              className="flex-1 px-6 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:bg-white/20 transition"
            />
            <button
              type="submit"
              disabled={newsletterLoading}
              className="px-8 py-3 bg-teal-600 text-white font-bold rounded-lg hover:bg-teal-500 transition shadow-lg shadow-teal-900/50 disabled:opacity-60 disabled:cursor-not-allowed"
            >
              {newsletterLoading ? 'Subscribing...' : 'Subscribe'}
            </button>
          </form>
          {(newsletterStatus || newsletterError) && (
            <div
              className={`mt-4 text-sm ${newsletterError ? 'text-red-400' : 'text-emerald-400'}`}
              role="status"
            >
              {newsletterError || newsletterStatus}
            </div>
          )}
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-gray-950 text-white py-16 border-t border-gray-800">
        <div className="max-w-7xl mx-auto px-6 lg:px-10">
          <div className="grid md:grid-cols-4 gap-12 mb-12">
            <div className="space-y-4">
              <div className="text-2xl font-bold tracking-tight">AR-FURNITURE</div>
              <p className="text-sm text-gray-400 leading-relaxed">
                {settings['landing.footer_tagline'] || 'Sustainable, modern furniture designed for comfort and style.'}
              </p>
            </div>
            <div>
              <h4 className="font-semibold text-lg mb-6 text-gray-200">Shop</h4>
              <ul className="space-y-3 text-gray-400">
                <li><a href="#" className="hover:text-teal-400 transition">Living Room</a></li>
                <li><a href="#" className="hover:text-teal-400 transition">Bedroom</a></li>
                <li><a href="#" className="hover:text-teal-400 transition">Kitchen</a></li>
                <li><a href="#" className="hover:text-teal-400 transition">Office</a></li>
              </ul>
            </div>
            <div>
              <h4 className="font-semibold text-lg mb-6 text-gray-200">Company</h4>
              <ul className="space-y-3 text-gray-400">
                <li><a href="#" className="hover:text-teal-400 transition">About Us</a></li>
                <li><a href="#" className="hover:text-teal-400 transition">Careers</a></li>
                <li><a href="#" className="hover:text-teal-400 transition">Blog</a></li>
                <li><a href="#" className="hover:text-teal-400 transition">Contact</a></li>
              </ul>
            </div>
            <div>
              <h4 className="font-semibold text-lg mb-6 text-gray-200">Legal</h4>
              <ul className="space-y-3 text-gray-400">
                <li><a href="#" className="hover:text-teal-400 transition">Terms of Service</a></li>
                <li><a href="#" className="hover:text-teal-400 transition">Privacy Policy</a></li>
                <li><a href="#" className="hover:text-teal-400 transition">Returns Policy</a></li>
              </ul>
            </div>
          </div>
          <div className="border-t border-gray-800 pt-8 flex flex-col md:flex-row items-center justify-between text-sm text-gray-500">
            <div>© 2026 AR-FURNITURE. All rights reserved.</div>
            <div className="flex gap-6 mt-4 md:mt-0">
              <a href="#" className="hover:text-white transition">Twitter</a>
              <a href="#" className="hover:text-white transition">Instagram</a>
              <a href="#" className="hover:text-white transition">Facebook</a>
            </div>
          </div>
        </div>
      </footer>
    </div>
  )
}
