<template>
<div id="app">
  <v-app id="inspire">
    <v-app-bar
      app
      color="success"
      dark
    >
      <v-toolbar-title>Application Challenge</v-toolbar-title>
    </v-app-bar>
        <v-content>
        <v-container fluid>
          <v-slide-y-transition mode="out-in">
            <router-view @showSnackBar="showSnackBar"></router-view>
          </v-slide-y-transition>
        </v-container>
      </v-content>
      <v-snackbar
          v-model="snackbar.snackbar"
          :bottom="snackbar.y === 'bottom'"
          :left="snackbar.x === 'left'"
          :multi-line="snackbar.mode === 'multi-line'"
          :right="snackbar.x === 'right'"
          :timeout="snackbar.timeout"
          :top="snackbar.y === 'top'"
          :color="color"
          :vertical="snackbar.mode === 'vertical'">
          {{ snackbar.text }}
          <v-btn
            v-if="snackbar.button && snackbar.button.enable" text :to="snackbar.button.action">
              {{snackbar.button.name}}
            </v-btn>
          <v-btn text @click="snackbar.snackbar = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-snackbar>
    <v-footer
      color="success"
      app
    >
      <span class="white--text">&copy; Alfredo Arratia </span>
    </v-footer>
  </v-app>
</div>
</template>

<script>
  import Meta from '@/mixins/meta'

  export default {
    mixins: [Meta],
    data () {
      return {
        drawer: null,
        appName: this.$configs.appName,
        appCompany: this.$configs.appCompany,
        appLogo: this.$configs.appLogo,
        snackbar: {
          color: null,
          snackbar: false,
          y: null,
          x: null,
          mode: null,
          timeout: null,
          text: null,
          button: {
            enable: false,
            action: null,
            name: null
          }
        },
      }
    },
    methods: {
      showSnackBar(snackBarData) {
        this.snackbar = snackBarData;
      },
    },
    props: {
      source: String
    }
  }
</script>

<style lang="scss">

</style>
