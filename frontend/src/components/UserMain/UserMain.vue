<template>
    <v-flex>
        <v-row align="center" justify="center" class="md6">
            <v-col cols="6">
                <v-text-field
                        v-model="search"
                        label="Поиск консультации"
                        single-line
                        v-on:keyup.enter="searchMethod"
                ></v-text-field>
            </v-col>
        </v-row>
        <template v-for="resultsPart in results">
            <v-row align="center" justify="center" class="md6">
                <v-col cols="3" v-for="item in resultsPart">
                    <v-card>
                        <v-row align="center" justify="center" class="md6">
                            <v-card-title class="headline">Услуга:{{item.title}}</v-card-title>
                            <v-card-text class="centred">
                                <div>
                                    <h3>Описание:</h3>
                                    <div>{{item.advert_description}}</div>
                                </div>
                                <div>
                                    <h3>Испольнитель:</h3>
                                    <div>{{`${item.first_name}`}}</div>
                                </div>
                                <div>
                                    <h3>Cтоимость:</h3>
                                    <div>{{`${item.cost}`}}</div>
                                </div>
                            </v-card-text>
                            <v-card-actions>
                                <v-btn color="primary" @click="getAdvert(item.id)">
                                    Получить
                                </v-btn>
                            </v-card-actions>
                        </v-row>
                    </v-card>
                </v-col>
            </v-row>
        </template>
    </v-flex>
</template>

<script>
  import { apiHost } from 'src/api/api.utils';
  import showNotify from 'src/helpers/showNotify';
  import qs from 'qs'
  import { splitEvery } from 'ramda';
  import { mapMutations } from 'vuex';

  export default {
    name: 'UserMain',
    data() {
      return {
        search: '',
        results: []
      }
    },
    methods: {
      ...mapMutations(['setCurrentAd']),
      getAdvert: async function (adId) {
        this.setCurrentAd(adId);
        if(this.$store.state.isAuthorized) {
          this.$router.push({path: `/advert/${adId}`});
        } else {
          this.$router.push({path: `/reg`});
        }

      },
      searchMethod: async function () {
        try {
          const res = await apiHost.post('/adverts-api/searched-list', qs.stringify({query: this.search}));
          if (res.is_success) {
            this.results = splitEvery(3, res.content.adverts);
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

      }
    }
  }
</script>

<style scoped>
    .centred {
        text-align:center;
    }

</style>
