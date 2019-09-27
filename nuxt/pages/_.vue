<template>
  <div>
    <h1>{{ title }}</h1>
    <div
      class="body"
      v-html="body"
    />
  </div>
</template>

<script>
export default {
  async asyncData ({ route, $axios }) {
    try {
      let path = route.params.pathMatch
        .split('/')
        .filter(item => item)

      let resource
      if (path[0] === 'articles' && path.length === 2) {
        resource = 'posts'
      } else if (path.length === 1) {
        resource = 'pages'
      } else {
        throw new Error('404')
      }
      let slug = path.reverse()[0]
      let res = await $axios.get(`${process.env.API_HOST}/wp-json/wp/v2/${resource}`, {
        params: {
          slug
        }
      })
      if (!res.data || !res.data.length) {
        throw new Error('404')
      }
      return {
        slug,
        page: res.data[0]
      }
    } catch (error) {
      console.error(error)
    }
  },
  computed: {
    title () {
      return this.page && this.page.title.rendered || '404'
    },
    body () {
      return this.page && this.page.content.rendered
    }
  }
}
</script>

<style>

</style>
