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

export const alertSuccess = (title, text) => Swal.fire({ icon: 'success', title, text })
export const alertError = (title, text) => Swal.fire({ icon: 'error', title, text })
export const alertInfo = (title, text) => Swal.fire({ icon: 'info', title, text })
export const alertWarning = (title, text) => Swal.fire({ icon: 'warning', title, text })

export const confirm = async ({ title = 'Are you sure?', text = '', confirmButtonText = 'Yes', cancelButtonText = 'Cancel', icon = 'warning' } = {}) => {
  const res = await Swal.fire({
    title,
    text,
    icon,
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText,
    cancelButtonText,
    reverseButtons: true,
  })
  return res.isConfirmed
}
