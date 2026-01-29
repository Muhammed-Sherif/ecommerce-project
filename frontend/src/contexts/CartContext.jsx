import React, { createContext, useContext, useState, useEffect } from 'react'
import { CartAPI } from '../api'
import { getAccessToken } from '../auth'

const CartContext = createContext()

export const useCart = () => {
  const context = useContext(CartContext)
  if (!context) {
    throw new Error('useCart must be used within a CartProvider')
  }
  return context
}

export const CartProvider = ({ children }) => {
  const [cartItems, setCartItems] = useState([])

  const hydrateFromApi = async (localItems) => {
    const token = getAccessToken()
    if (!token) return
    try {
      const { data } = await CartAPI.getCart()
      if (!data?.success) return
      const apiItems = data.cart || data.items || []
      if (!Array.isArray(apiItems)) return

      const localById = new Map((localItems || []).map(item => [item.id, item]))
      const normalized = apiItems.map(item => {
        const productId = item.product_id ?? item.productId ?? item.id
        const quantity = item.quantity ?? 1
        const local = localById.get(productId)
        if (local) {
          return { ...local, quantity }
        }
        const price = Number(item.price ?? local?.price ?? 0)
        return {
          id: productId,
          name: item.name || `Product #${productId}`,
          price,
          image: item.image || '',
          quantity
        }
      })
      setCartItems(normalized)
    } catch (error) {
      console.error('Failed to load cart from API:', error)
    }
  }

  // Load cart from localStorage on mount, then hydrate from API if logged in
  useEffect(() => {
    const savedCart = localStorage.getItem('cart')
    const localItems = savedCart ? JSON.parse(savedCart) : []
    if (localItems.length) {
      setCartItems(localItems)
    }
    hydrateFromApi(localItems)
  }, [])

  // Save cart to localStorage whenever it changes
  useEffect(() => {
    localStorage.setItem('cart', JSON.stringify(cartItems))
  }, [cartItems])

  const addToCart = (product) => {
    setCartItems(prevItems => {
      const existingItem = prevItems.find(item => item.id === product.id)
      if (existingItem) {
        return prevItems.map(item =>
          item.id === product.id
            ? { ...item, quantity: item.quantity + 1 }
            : item
        )
      } else {
        return [...prevItems, { ...product, quantity: 1, price: Number(product.price) }]
      }
    })
    const token = getAccessToken()
    if (token) {
      CartAPI.addItem({ product_id: product.id, quantity: 1 }).catch(error => {
        console.error('Failed to add item to cart:', error)
      })
    }
  }

  const updateQuantity = (id, quantity) => {
    if (quantity < 1) return
    setCartItems(prevItems =>
      prevItems.map(item =>
        item.id === id ? { ...item, quantity } : item
      )
    )
    const token = getAccessToken()
    if (token) {
      CartAPI.updateItem({ product_id: id, quantity }).catch(error => {
        console.error('Failed to update cart item:', error)
      })
    }
  }

  const removeFromCart = (id) => {
    setCartItems(prevItems => prevItems.filter(item => item.id !== id))
    const token = getAccessToken()
    if (token) {
      CartAPI.removeItem({ product_id: id }).catch(error => {
        console.error('Failed to remove cart item:', error)
      })
    }
  }

  const getCartCount = () => {
    return cartItems.reduce((total, item) => total + item.quantity, 0)
  }

  const getCartTotal = () => {
    return cartItems.reduce((total, item) => total + item.price * item.quantity, 0)
  }

  const clearCart = () => {
    setCartItems([])
    const token = getAccessToken()
    if (token) {
      CartAPI.clear().catch(error => {
        console.error('Failed to clear cart:', error)
      })
    }
  }

  const value = {
    cartItems,
    addToCart,
    updateQuantity,
    removeFromCart,
    getCartCount,
    getCartTotal,
    clearCart
  }

  return (
    <CartContext.Provider value={value}>
      {children}
    </CartContext.Provider>
  )
}
