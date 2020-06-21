<template>
<div id="app">
  <v-app id="inspire">
    <v-app id="inspire">
      <v-navigation-drawer
        v-model="drawer"
        app
      >
        <v-list dense>
          <v-list-item to="/">
            <v-list-item-action>
              <v-icon>mdi-home</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>
                 Home
              </v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item to="/list">
            <v-list-item-action>
              <v-icon>mdi-email</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title >
                List
              </v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-navigation-drawer>
  
      <v-app-bar
        app
        color="indigo"
        dark
      >
        <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
        <v-toolbar-title>Application Challenge</v-toolbar-title>
      </v-app-bar>
  
      <v-main>
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
              :color="'info'"
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
      </v-main>
      <v-footer
        color="indigo"
        app
      >
        <span class="white--text">&copy; Alfredo Arratia </span>
      </v-footer>
    </v-app>
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
