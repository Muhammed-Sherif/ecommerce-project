export const getAccessToken = () => localStorage.getItem('access_token')
export const setAccessToken = (t) => localStorage.setItem('access_token', t)
export const getRefreshToken = () => localStorage.getItem('refresh_token')
export const setRefreshToken = (t) => localStorage.setItem('refresh_token', t)
export const clearAuth = () => {
  localStorage.removeItem('access_token')
  localStorage.removeItem('refresh_token')
  localStorage.removeItem('user')
}
