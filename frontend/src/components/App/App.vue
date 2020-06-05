<template>
    <div id="app">
        <v-app>
            <div class="overlay" v-if="$store.state.loader.status">
                <h2 class="loader-text">{{$store.state.loader.message}}</h2>
                <v-progress-circular
                        :size="70"
                        :width="7"
                        color="purple"
                        indeterminate
                        class="spinner-loader"
                ></v-progress-circular>
            </div>
            <notifications group="main"></notifications>
            <div>
                <v-toolbar color="primary">
                    <v-spacer></v-spacer>

                    <v-btn v-for="item in getMenuItems()"
                           class="ma-2"
                           :key="item.title"
                           :to="item.href ? item.href : null"
                           @click="item.fn ? item.fn() : null"
                           style=""
                           outlined>
                        <v-icon left>{{ item.icon }}</v-icon>
                        {{ item.title }}
                    </v-btn>

                    <v-spacer></v-spacer>
                </v-toolbar>
            </div>
            <v-content>
                <v-container fluid grid-list-md text-xs-center>
                    <router-view></router-view>
                </v-container>
            </v-content>
            <v-footer color="primary lighten-1"
                      padless>
                <v-row justify="center" no-gutters>
                    <v-col cols="12" class="primary py-4 text-center white--text">&copy;2020 —
                        <strong>/zero</strong></v-col>
                </v-row>
            </v-footer>
        </v-app>
    </div>
</template>

<script>
  import { apiHost } from 'src/api/api.utils';
  import showNotify from 'src/helpers/showNotify';
  import { mapMutations } from 'vuex';

  export default {
    name: 'App',
    beforeCreate: async function () {
      const res = await apiHost.post('/site-api/get-user-info');
      if (res.is_success) {
        if (res.content.is_guest) {
          this.setIsAuthorized(false);
          this.$router.push({path: '/'});
        } else {
          showNotify({
            text: 'Успешно авторизованы',
            type: 'success'
          });
          this.setUserData(res.content);
          this.setIsAuthorized(true);
          this.$router.push({
            path: res.content.type === 2
                  ? 'specialist-main'
                  : 'user-main'
          });
        }
      } else {
        showNotify({
          text: res.errors[0]
                ? res.errors[0]
                : 'Произошла ошибка',
          type: 'error'
        })
      }
    },
    data() {
      return {};
    },
    methods: {
      ...mapMutations(['setIsAuthorized', 'setUserData']),
      logout: async function () {
        try {
          const res = await apiHost.post('/site-api/logout');
          if (res.is_success) {
            showNotify({
              text: 'Успешно разлогинены',
              type: 'success'
            });
            this.setIsAuthorized(false);
            this.$router.push({path: '/'});
          } else {
            showNotify({
              text: 'Произошла ошибка',
              type: 'error'
            })

          }
        } catch (e) {
          showNotify({
            text: 'Произошла ошибка',
            type: 'error'
          })
        }
      },
      gotToMain: async function () {
        if (!this.$store.state.isAuthorized) {
          this.$router.push({path: '/'});
          return;
        }
        this.$router.push({path: '/user-main'});

        // if (this.$store.state.userData.type === 2) {
        //   this.$router.push({path: '/specialist-main'});
        // } else {
        //   this.$router.push({path: '/user-main'});
        // }
      },
      getMenuItems() {
        return this.$store.state.isAuthorized
               ? [{
            title: 'Главная',
            href: '',
            fn: this.gotToMain,
            icon: 'fa-home'
          }, {
            title: 'Выйти',
            href: '',
            fn: this.logout,
            icon: 'fa-sign-out'
          }]
               : [{
            title: 'Главная',
            href: '',
            fn: this.gotToMain,
            icon: 'fa-home'
          }, {
            title: 'Авторизация',
            href: '/auth',
            icon: 'fa-user-o'
          }];
      }
    }
  };
</script>

<style scoped>
    .loader-text {
        color: white;
        position: absolute;
        top: 40%;
    }

    .overlay {
        background: rgba(0, 0, 0, 0.4);
        width: 100%;
        height: 100%;
        position: absolute;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
</style>
