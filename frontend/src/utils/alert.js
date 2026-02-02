import Swal from 'sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 2500,
  timerProgressBar: true,
})

export const toastSuccess = (title = 'Success') => Toast.fire({ icon: 'success', title })
export const toastError = (title = 'Error') => Toast.fire({ icon: 'error', title })
export const toastInfo = (title = 'Info') => Toast.fire({ icon: 'info', title })
export const toastWarning = (title = 'Warning') => Toast.fire({ icon: 'warning', title })

const defaultConfirmColor = '#60a5fa' // light blue (tailwind blue-400)
const defaultCancelColor = '#9ca3af'  // gray (tailwind gray-400)

export const alertSuccess = (title, text) =>
  Swal.fire({ icon: 'success', title, text, confirmButtonText: 'OK', confirmButtonColor: defaultConfirmColor })
export const alertError = (title, text) =>
  Swal.fire({ icon: 'error', title, text, confirmButtonText: 'OK', confirmButtonColor: defaultConfirmColor })
export const alertInfo = (title, text) =>
  Swal.fire({ icon: 'info', title, text, confirmButtonText: 'OK', confirmButtonColor: defaultConfirmColor })
export const alertWarning = (title, text) =>
  Swal.fire({ icon: 'warning', title, text, confirmButtonText: 'OK', confirmButtonColor: defaultConfirmColor })

export const confirm = async ({ title = 'Are you sure?', text = '', confirmButtonText = 'OK', cancelButtonText = 'Cancel', icon = 'warning' } = {}) => {
  const res = await Swal.fire({
    title,
    text,
    icon,
    showCancelButton: true,
    confirmButtonColor: defaultConfirmColor,
    cancelButtonColor: defaultCancelColor,
    confirmButtonText,
    cancelButtonText,
    // Keep OK on the left and Cancel on the right for LTR locales
    reverseButtons: true,
  })
  return res.isConfirmed
}
