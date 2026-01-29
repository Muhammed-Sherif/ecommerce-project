import React from 'react'
import { Link, useNavigate } from 'react-router-dom'
import { useCart } from '../contexts/CartContext'
import { CheckoutAPI, CouponAPI } from '../api'
import { getAccessToken } from '../auth'
import Header from '../components/Header'

export default function Cart() {
  const { cartItems, updateQuantity, removeFromCart, getCartTotal, clearCart } = useCart()
  const navigate = useNavigate()
  const [isProcessing, setIsProcessing] = React.useState(false)
  const [couponCode, setCouponCode] = React.useState('')
  const [appliedCoupon, setAppliedCoupon] = React.useState(null)
  const [couponError, setCouponError] = React.useState('')
  const [isApplyingCoupon, setIsApplyingCoupon] = React.useState(false)

  const subtotal = getCartTotal()

  const discount = appliedCoupon
    ? (appliedCoupon.discountType === 'percent'
      ? (subtotal * (appliedCoupon.discountValue / 100))
      : appliedCoupon.discountValue)
    : 0

  const shipping = subtotal > 500 ? 0 : 50
  const total = Math.max(0, subtotal - discount + shipping)

  const handleApplyCoupon = async () => {
    if (!couponCode.trim()) return

    setIsApplyingCoupon(true)
    setCouponError('')

    try {
      const { data } = await CouponAPI.getByCode(couponCode)
      if (data.success) {
        setAppliedCoupon(data.copon)
        alert('Coupon applied successfully!')
      } else {
        setCouponError(data.message || 'Invalid coupon code')
      }
    } catch (error) {
      console.error('Coupon error:', error)
      setCouponError('Failed to validate coupon')
    } finally {
      setIsApplyingCoupon(false)
    }
  }

  const handleCheckout = async () => {
    if (cartItems.length === 0) return

    if (!getAccessToken()) {
      alert('Please login to checkout.')
      navigate('/login', { state: { from: '/cart' } })
      return
    }

    setIsProcessing(true)

    // User data from localStorage or auth context
    const user = JSON.parse(localStorage.getItem('user') || '{}')

    const payload = {
      coupon_code: appliedCoupon?.code || null,
      shipping_address: {
        street: user.shipping_street || "NA",
        city: user.shipping_city || "NA",
        state: user.shipping_state || "NA",
        country: user.shipping_country || "NA",
        zip_code: user.shipping_zip_code || "NA"
      }
    }

    try {
      const { data } = await CheckoutAPI.checkout(payload)

      if (data.success && data.payment_url) {
        alert('Checkout successful! Redirecting to payment...')
        window.location.href = data.payment_url
      } else {
        alert(data.message || 'Checkout failed')
      }
    } catch (error) {
      console.error("Checkout failed:", error)
      alert("Checkout failed: " + (error.response?.data?.message || error.message))
    } finally {
      setIsProcessing(false)
    }
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="max-w-7xl mx-auto px-6 lg:px-10 py-8">
        {cartItems.length === 0 ? (
          <div className="text-center py-16">
            <div className="text-6xl mb-4">🛒</div>
            <h2 className="text-2xl font-semibold text-gray-900 mb-2">Your cart is empty</h2>
            <p className="text-gray-600 mb-6">Add some products to get started!</p>
            <Link
              to="/"
              className="inline-flex items-center px-6 py-3 bg-teal-600 text-white font-medium rounded hover:bg-teal-700 transition"
            >
              Continue Shopping
            </Link>
          </div>
        ) : (
          <div className="grid lg:grid-cols-3 gap-8">
            {/* Cart Items */}
            <div className="lg:col-span-2 space-y-4">
              {cartItems.map((item) => (
                <div key={item.id} className="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                  <div className="flex gap-4">
                    <img
                      src={item.image}
                      alt={item.name}
                      className="w-24 h-24 object-cover rounded-lg"
                    />
                    <div className="flex-1">
                      <h3 className="text-lg font-semibold text-gray-900">{item.name}</h3>
                      <p className="text-gray-600">${Number(item.price).toFixed(2)}</p>
                      <div className="flex items-center gap-4 mt-4">
                        <div className="flex items-center gap-2">
                          <button
                            onClick={() => updateQuantity(item.id, item.quantity - 1)}
                            className="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center"
                          >
                            -
                          </button>
                          <span className="w-8 text-center">{item.quantity}</span>
                          <button
                            onClick={() => updateQuantity(item.id, item.quantity + 1)}
                            className="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center"
                          >
                            +
                          </button>
                        </div>
                        <button
                          onClick={() => removeFromCart(item.id)}
                          className="text-red-600 hover:text-red-700 text-sm font-medium"
                        >
                          Remove
                        </button>
                      </div>
                    </div>
                    <div className="text-right">
                      <p className="text-lg font-semibold text-gray-900">
                        ${(item.price * item.quantity).toFixed(2)}
                      </p>
                    </div>
                  </div>
                </div>
              ))}
            </div>

            {/* Order Summary */}
            <div className="bg-white rounded-lg p-6 shadow-sm border border-gray-200 h-fit">
              <h2 className="text-xl font-semibold text-gray-900 mb-6">Order Summary</h2>
              <div className="space-y-4">
                <div className="flex justify-between">
                  <span className="text-gray-600">Subtotal</span>
                  <span className="text-gray-900">${subtotal.toFixed(2)}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-gray-600">Shipping</span>
                  <span className="text-gray-900">
                    {shipping === 0 ? 'Free' : `$${shipping.toFixed(2)}`}
                  </span>
                </div>

                {discount > 0 && (
                  <div className="flex justify-between text-green-600">
                    <span>Discount ({appliedCoupon.code})</span>
                    <span>-${discount.toFixed(2)}</span>
                  </div>
                )}

                <div className="pt-4">
                  <div className="flex gap-2">
                    <input
                      type="text"
                      placeholder="Coupon code"
                      className="flex-1 px-3 py-2 border rounded text-sm focus:ring-teal-500 focus:border-teal-500"
                      value={couponCode}
                      onChange={(e) => setCouponCode(e.target.value)}
                    />
                    <button
                      onClick={handleApplyCoupon}
                      disabled={isApplyingCoupon || !couponCode}
                      className="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded hover:bg-gray-200 disabled:opacity-50"
                    >
                      {isApplyingCoupon ? '...' : 'Apply'}
                    </button>
                  </div>
                  {couponError && <p className="text-red-500 text-xs mt-1">{couponError}</p>}
                </div>

                <hr className="border-gray-200" />
                <div className="flex justify-between text-lg font-semibold">
                  <span className="text-gray-900">Total</span>
                  <span className="text-gray-900">${total.toFixed(2)}</span>
                </div>
              </div>
              <button
                onClick={handleCheckout}
                disabled={isProcessing}
                className="w-full mt-6 px-6 py-3 bg-teal-600 text-white font-medium rounded hover:bg-teal-700 transition disabled:bg-teal-400 disabled:cursor-not-allowed flex items-center justify-center gap-2"
              >
                {isProcessing ? (
                  <>
                    <svg className="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                      <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                  </>
                ) : (
                  'Proceed to Checkout'
                )}
              </button>
              <Link
                to="/"
                className="block text-center mt-4 text-teal-600 hover:text-teal-700 text-sm font-medium"
              >
                Continue Shopping
              </Link>
            </div>
          </div>
        )}
      </div>
    </div>
  )
}
