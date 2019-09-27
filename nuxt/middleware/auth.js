export default function ({ $axios, route }) {
  if (!route.query || !route.query.preview_token) {
    return
  }
  let token = route.query.preview_token
  $axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
  if (!$axios.defaults.params) {
    $axios.defaults.params = {}
  }
  $axios.onRequest((config) => ({
    ...config,
    params: {
      status: [
        'publish',
        'future',
        'draft',
        'pending',
        'private'
      ]
    }
  }))
}
