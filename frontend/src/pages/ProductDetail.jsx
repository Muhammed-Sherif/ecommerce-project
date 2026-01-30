import React, { useState, useEffect } from 'react'
import { useParams, Link, useNavigate } from 'react-router-dom'
import { useCart } from '../contexts/CartContext'
import { CommentsAPI, ReviewsAPI, ProductsAPI, InventoryAPI } from '../api'
import { getAccessToken } from '../auth'
import Header from '../components/Header'
import echo from '../realtime/echo'

export default function ProductDetail() {
  const { id } = useParams()
  const navigate = useNavigate()
  const { addToCart, getCartCount } = useCart()

  const [product, setProduct] = useState(null)
  const [reviews, setReviews] = useState([])
  const [averageRating, setAverageRating] = useState(0)
  const [comments, setComments] = useState([])
  const [inventory, setInventory] = useState(null)
  const [loading, setLoading] = useState(true)
  const [selectedImage, setSelectedImage] = useState(0)
  const [quantity, setQuantity] = useState(1)
  const [showReviewForm, setShowReviewForm] = useState(false)
  const [reviewForm, setReviewForm] = useState({
    rating: 5,
    title: '',
    comment: ''
  })
  const [commentForm, setCommentForm] = useState({
    content: ''
  })
  const [isSubmittingReview, setIsSubmittingReview] = useState(false)
  const [isSubmittingComment, setIsSubmittingComment] = useState(false)

  useEffect(() => {
    const fetchProduct = async () => {
      setLoading(true)
      try {
        const [productRes, inventoryRes] = await Promise.all([
          ProductsAPI.getById(id),
          InventoryAPI.getByProduct(id).catch(() => null)
        ])

        const rawProduct = productRes?.data?.product || productRes?.data || null
        if (productRes?.data?.success === false) {
          setProduct(null)
        } else {
          const images = Array.isArray(rawProduct?.images) && rawProduct.images.length > 0
            ? rawProduct.images
            : rawProduct?.image
              ? [rawProduct.image]
              : []
          setProduct(rawProduct ? { ...rawProduct, images } : null)
          setSelectedImage(0)
        }

        const inventoryData = inventoryRes?.data?.inventory
        setInventory(inventoryData || { quantity: 0, reserved_quantity: 0 })
      } catch (error) {
        console.error('Failed to load product:', error)
        setProduct(null)
        setInventory({ quantity: 0, reserved_quantity: 0 })
      } finally {
        setLoading(false)
      }
    }

    fetchProduct()
  }, [id])

  useEffect(() => {
    const fetchReviewsAndComments = async () => {
      try {
        const [reviewsRes, commentsRes] = await Promise.all([
          ReviewsAPI.getByProduct(id),
          CommentsAPI.getByProduct(id)
        ])

        if (reviewsRes?.data?.success) {
          setReviews(reviewsRes.data.reviews || [])
          setAverageRating(Number(reviewsRes.data.average_rating) || 0)
        }

        if (commentsRes?.data?.success) {
          setComments(commentsRes.data.comments || [])
        }
      } catch (error) {
        console.error('Failed to load reviews/comments:', error)
      }
    }

    fetchReviewsAndComments()
  }, [id])

  useEffect(() => {
    if (!id) return
    const channelName = `.reviews.product.${id}`
    const channel = echo.channel(channelName)

    const onReviewCreated = (event) => {
      const incoming = event?.review
      if (!incoming) return
      setReviews((prev) => {
        if (prev.some((r) => r.id === incoming.id)) return prev
        const next = [incoming, ...prev]
        const avg =
          next.length > 0
            ? next.reduce((sum, r) => sum + Number(r.rating || 0), 0) / next.length
            : 0
        setAverageRating(Number(avg.toFixed(1)) || 0)
        return next
      })
    }

    channel.listen('.review.created', onReviewCreated)

    return () => {
      channel.stopListening('.review.created', onReviewCreated)
      echo.leave(channelName)
    }
  }, [id])

  const handleAddToCart = () => {
    if (product) {
      addToCart({ ...product, quantity })
      alert(`${quantity} x ${product.name} added to cart!`)
    }
  }

  const handleSubmitReview = async (e) => {
    e.preventDefault()
    if (!getAccessToken()) {
      alert('Please login to submit a review.')
      navigate('/login')
      return
    }

    setIsSubmittingReview(true)
    try {
      const payload = {
        product_id: parseInt(id, 10),
        rating: reviewForm.rating,
        title: reviewForm.title,
        comment: reviewForm.comment
      }
      const { data } = await ReviewsAPI.create(payload)
      if (data?.success) {
        setShowReviewForm(false)
        setReviewForm({ rating: 5, title: '', comment: '' })
        const refreshed = await ReviewsAPI.getByProduct(id)
        if (refreshed?.data?.success) {
          setReviews(refreshed.data.reviews || [])
          setAverageRating(Number(refreshed.data.average_rating) || 0)
        }
      }
    } catch (error) {
      console.error('Review submit failed:', error)
    } finally {
      setIsSubmittingReview(false)
    }
  }

  const handleSubmitComment = async (e) => {
    e.preventDefault()
    if (!getAccessToken()) {
      alert('Please login to comment.')
      navigate('/login')
      return
    }

    if (!commentForm.content.trim()) return
    setIsSubmittingComment(true)
    try {
      const payload = {
        product_id: parseInt(id, 10),
        content: commentForm.content
      }
      const { data } = await CommentsAPI.create(payload)
      if (data?.success) {
        setCommentForm({ content: '' })
        const refreshed = await CommentsAPI.getByProduct(id)
        if (refreshed?.data?.success) {
          setComments(refreshed.data.comments || [])
        }
      }
    } catch (error) {
      console.error('Comment submit failed:', error)
    } finally {
      setIsSubmittingComment(false)
    }
  }

  const StarRating = ({ rating, size = 'sm', interactive = false, onChange }) => {
    const sizeClasses = { sm: 'w-4 h-4', md: 'w-5 h-5', lg: 'w-6 h-6' }
    return (
      <div className="flex gap-1">
        {[1, 2, 3, 4, 5].map((star) => (
          <svg
            key={star}
            onClick={() => interactive && onChange && onChange(star)}
            className={`${sizeClasses[size]} ${star <= rating ? 'text-yellow-400 fill-current' : 'text-gray-300'} ${interactive ? 'cursor-pointer' : ''}`}
            viewBox="0 0 20 20"
          >
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
          </svg>
        ))}
      </div>
    )
  }

  if (loading) return <div className="min-h-screen flex items-center justify-center">Loading...</div>
  if (!product) return <div className="min-h-screen flex items-center justify-center">Product not found</div>

  const availableStock = inventory ? inventory.quantity - inventory.reserved_quantity : 0

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      <div className="max-w-7xl mx-auto px-6 lg:px-10 py-12">
        <div className="grid lg:grid-cols-2 gap-12 mb-16">
          <div className="space-y-4">
            <div className="bg-white rounded-lg p-8 shadow-sm text-center">
              <img src={product.images[selectedImage]} alt={product.name} className="max-h-96 mx-auto object-contain" />
            </div>
            {product.images.length > 1 && (
              <div className="grid grid-cols-4 gap-4">
                {product.images.map((img, idx) => (
                  <button key={idx} onClick={() => setSelectedImage(idx)} className={`bg-white rounded-lg p-2 border-2 ${selectedImage === idx ? 'border-teal-600' : 'border-transparent'}`}>
                    <img src={img} alt="thumb" className="h-16 mx-auto object-contain" />
                  </button>
                ))}
              </div>
            )}
          </div>

          <div className="space-y-6">
            <div>
              <div className="flex items-center gap-2 mb-2">
                <StarRating rating={Math.round(averageRating)} size="md" />
                <span className="text-sm text-gray-600">({reviews.length} reviews)</span>
              </div>
              <h1 className="text-4xl font-bold text-gray-900 mb-2">{product.name}</h1>
              <p className="text-sm text-gray-500 uppercase">{product.category}</p>
            </div>
            <div className="text-3xl font-bold text-teal-600">${Number(product.price).toFixed(2)}</div>
            <p className="text-gray-700 leading-relaxed">{product.description}</p>
            <div className="flex items-center gap-2">
              <div className={`w-3 h-3 rounded-full ${availableStock > 0 ? 'bg-green-500' : 'bg-red-500'}`}></div>
              <span className="text-sm text-gray-700">{availableStock > 0 ? `In Stock (${availableStock} available)` : 'Out of Stock'}</span>
            </div>
            <div className="flex gap-4 pt-4">
              <div className="flex items-center border border-gray-300 rounded">
                <button onClick={() => setQuantity(Math.max(1, quantity - 1))} className="px-4 py-2 hover:bg-gray-100">-</button>
                <span className="px-6 py-2 border-x border-gray-300">{quantity}</span>
                <button onClick={() => setQuantity(Math.min(availableStock, quantity + 1))} className="px-4 py-2 hover:bg-gray-100">+</button>
              </div>
              <button onClick={handleAddToCart} disabled={availableStock === 0} className="flex-1 px-8 py-3 bg-teal-600 text-white font-medium rounded hover:bg-teal-700 transition disabled:bg-gray-300">
                Add to Cart
              </button>
            </div>
          </div>
        </div>

        <div className="bg-white rounded-lg shadow-sm p-8 mb-8">
          <div className="flex items-center justify-between mb-8">
            <h2 className="text-2xl font-bold text-gray-900">Customer Reviews</h2>
            <button onClick={() => setShowReviewForm(!showReviewForm)} className="px-6 py-3 bg-teal-600 text-white font-medium rounded">Write a Review</button>
          </div>

          {showReviewForm && (
            <form onSubmit={handleSubmitReview} className="mb-8 p-6 bg-gray-50 rounded-lg space-y-4">
              <StarRating rating={reviewForm.rating} size="lg" interactive onChange={(r) => setReviewForm({ ...reviewForm, rating: r })} />
              <input type="text" placeholder="Title" value={reviewForm.title} onChange={(e) => setReviewForm({ ...reviewForm, title: e.target.value })} className="w-full p-2 border rounded" required />
              <textarea placeholder="Comment" value={reviewForm.comment} onChange={(e) => setReviewForm({ ...reviewForm, comment: e.target.value })} rows={4} className="w-full p-2 border rounded" required />
              <div className="flex gap-2">
                <button type="submit" className="px-6 py-2 bg-teal-600 text-white rounded">Submit</button>
                <button type="button" onClick={() => setShowReviewForm(false)} className="px-6 py-2 border rounded">Cancel</button>
              </div>
            </form>
          )}

          <div className="space-y-6">
            {reviews.map((r) => (
              <div key={r.id} className="border-b pb-6 last:border-0">
                <div className="flex justify-between mb-2">
                  <div className="font-semibold">{r.user_name}</div>
                  <StarRating rating={r.rating} size="sm" />
                </div>
                <h4 className="font-medium mb-1">{r.title}</h4>
                <p className="text-gray-700">{r.comment}</p>
              </div>
            ))}
          </div>
        </div>

        <div className="bg-white rounded-lg shadow-sm p-8">
          <h2 className="text-2xl font-bold mb-6">Questions & Comments</h2>
          <form onSubmit={handleSubmitComment} className="mb-6 space-y-3">
            <textarea value={commentForm.content} onChange={(e) => setCommentForm({ content: e.target.value })} rows={3} placeholder="Ask a question..." className="w-full p-3 border rounded" />
            <div className="flex justify-end">
              <button type="submit" className="px-5 py-2 bg-gray-900 text-white rounded">Post Comment</button>
            </div>
          </form>
          <div className="space-y-4">
            {comments.map((c) => (
              <div key={c.id} className="border-b pb-4 last:border-0">
                <div className="flex justify-between text-xs text-gray-500 mb-1">
                  <span>User #{c.user_id}</span>
                  <span>{c.created_at}</span>
                </div>
                <p>{c.content}</p>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  )
}
