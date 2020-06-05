<template>

        <v-row align="center" justify="center" class="md6">
            <v-col cols="4">
                <v-card>
                    <v-img
                            :src="images.findConsult"
                            class="white--text align-end"
                            gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                            height="200px"
                    ></v-img>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn x-large color="secondary" to="/user-main">Найти консультацию</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-col>
            <v-col cols="4">
                <v-card>
                    <v-img
                            :src="images.findJob"
                            class="white--text align-end"
                            gradient="to bottom, rgba(0,0,0,.1), rgba(0,0,0,.5)"
                            height="200px"
                    ></v-img>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn x-large color="secondary" @click="specialistGoToNextScreen">
                            Найти работу
                        </v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>

</template>

<script>
  import startScreen from 'src/assets/start-screen.png';


  export default {
    name: 'Home',
    components: {},
    data() {
      return {
        images: {
          findJob: startScreen,
          findConsult: startScreen

        }
      }
    },
    computed: {},
    methods: {
      specialistGoToNextScreen: function () {
        this.$router.push({
          path: this.$store.state.isAuthorized
                ? 'specialist-main'
                : 'specialist-register'
        });
      }
    }
  };
</script>

<style scoped>
</style>
