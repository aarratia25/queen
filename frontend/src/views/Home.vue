<template>
   <v-row class="fill-height">
      <v-col>
        <v-sheet height="64">
          <v-toolbar flat color="white">
            <v-btn outlined class="mr-4" color="grey darken-2" @click="setToday">
              Today
            </v-btn>
            <v-btn v-if="type == 'day'" outlined class="mr-4" color="grey darken-2" @click="storeDate(start)">
              Store
            </v-btn>
            <v-btn fab text small color="grey darken-2" @click="prev">
              <v-icon small>mdi-chevron-left</v-icon>
            </v-btn>
            <v-btn fab text small color="grey darken-2" @click="next">
              <v-icon small>mdi-chevron-right</v-icon>
            </v-btn>
            <v-toolbar-title>{{ title }}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-menu bottom right>
              <template v-slot:activator="{ on }">
                <v-btn
                  outlined
                  color="grey darken-2"
                  v-on="on"
                >
                  <span>{{ typeToLabel[type] }}</span>
                  <v-icon right>mdi-menu-down</v-icon>
                </v-btn>
              </template>
              <v-list>
                <v-list-item @click="type = 'day'">
                  <v-list-item-title>Day</v-list-item-title>
                </v-list-item>
                <v-list-item @click="type = 'week'">
                  <v-list-item-title>Week</v-list-item-title>
                </v-list-item>
                <v-list-item @click="type = 'month'">
                  <v-list-item-title>Month</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </v-toolbar>
        </v-sheet>
        <v-sheet height="600">
          <v-calendar
            ref="calendar"
            v-model="focus"
            color="primary"
            :events="events"
            :event-color="getEventColor"
            :type="type"
            @click:event="showEvent"
            @click:more="viewDay"
            @click:date="viewDay"
            @change="updateRange"
            :min="nowDate"
          ></v-calendar>
          <v-menu
            v-model="selectedOpen"
            :close-on-content-click="false"
            :activator="selectedElement"
            offset-x
          >
            <v-card
              color="grey lighten-4"
              min-width="350px"
              flat
            >
              <v-toolbar
                :color="selectedEvent.color"
                dark
              >
                <v-btn icon @click="updateDate(selectedEvent)">
                  <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-toolbar-title v-html="selectedEvent.email"></v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn icon @click="dialogDelete = true">
                  <v-icon>delete</v-icon>
                </v-btn>
              </v-toolbar>
              <v-card-text>
                <span v-html="selectedEvent.details"></span>
              </v-card-text>
              <v-card-actions>
                <v-btn
                  text
                  color="secondary"
                  @click="selectedOpen = false"
                >
                  Cancel
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-menu>
        </v-sheet>
      </v-col>
          <v-dialog v-model="dialog" width="500">
      <v-card>
        <v-card-title class="headline grey lighten-2" primary-title>
          {{ storeOrUpdate }}
        </v-card-title>

        <v-card-text>
         <v-container>
          <v-row justify="center" v-if="picker">
            <v-time-picker v-model="hours" color="success"></v-time-picker>
          </v-row>
          <v-form ref="form" v-model="form.valid" lazy-validation >
              <v-text-field
                  prepend-icon="person" 
                  v-model="form.name" 
                  :counter="10" 
                  :rules="nameRules" 
                  label="Name" required>
              </v-text-field>
              <v-text-field
                  prepend-icon="email" 
                  v-model="form.email" 
                  :counter="40" 
                  :rules="emailRules" 
                  label="Email" required>
              </v-text-field>
              <v-text-field
                  type="text"
                  prepend-icon="event" 
                  v-model="hours" 
                  label="Start" readonly @click="showPicker()">
              </v-text-field>
              <br>
              <v-btn :loading="loading" :disabled="!form.valid" color="success" class="mr-4" @click="validate">
                  {{ storeOrUpdate }}
              </v-btn>
            </v-form>
         </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogDelete" persistent max-width="290">
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          color="primary"
          dark
          v-bind="attrs"
          v-on="on"
        >
          Delete 
        </v-btn>
      </template>
      <v-card>
        <v-card-title class="headline">Delete this element</v-card-title>
        <v-card-text>Are you sure to delete?</v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="green darken-1" text @click="dialogDelete = false">Disagree</v-btn>
          <v-btn color="red darken-1" text @click="deleteDate(selectedEvent)">Agree</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    </v-row>
</template>

<script>
import axios from "axios";
  export default {
   data: () => ({
     dialogDelete:false,
    nowDate: new Date().toISOString().substr(0, 10),
    loading: false,
    dialog:false,
    picker:false,
    storeOrUpdate: 'Store',
    hours: null,
    dateTimeString:null,
    form: {
      id:null,
      is: 'create',
      valid: true,
      name: null,
      email: null,
      queen_datetime: null,
    },
    nameRules: [
      v => !!v || 'Name required',
      v => (v && v.length <= 10) || 'The name must not be more than 10 characters.',
    ],
    emailRules: [
      v => !!v || 'Email Required',
      v => /.+@.+\..+/.test(v) || 'Invalid email format',
      v => (v && v.length <= 40) || 'The email should not be more than 40 characters.',
    ],
    queenDatetimeRules: [
        v => !!v || 'Datetime required',
    ],
    focus: '',
    type: 'month',
    typeToLabel: {
      month: 'Month',
      week: 'Week',
      day: 'Day',
      '4day': '4 Days',
    },
    start: null,
    end: null,
    selectedEvent: {},
    selectedElement: null,
    selectedOpen: false,
    events: [],
    colors: ['blue', 'indigo', 'deep-purple', 'cyan', 'green', 'orange', 'grey darken-1'],
    names: ['Meeting', 'Holiday', 'PTO', 'Travel', 'Event', 'Birthday', 'Conference', 'Party'],
  }),
  computed: {
    title () {
      const { start, end } = this
      if (!start || !end) {
        return ''
      }

      const startMonth = this.monthFormatter(start)
      const endMonth = this.monthFormatter(end)
      const suffixMonth = startMonth === endMonth ? '' : endMonth

      const startYear = start.year
      const endYear = end.year
      const suffixYear = startYear === endYear ? '' : endYear

      const startDay = start.day + this.nth(start.day)
      const endDay = end.day + this.nth(end.day)

      switch (this.type) {
        case 'month':
          return `${startMonth} ${startYear}`
        case 'week':
        case '4day':
          return `${startMonth} ${startDay} ${startYear} - ${suffixMonth} ${endDay} ${suffixYear}`
        case 'day':
          return `${startMonth} ${startDay} ${startYear}`
      }
      return ''
    },
    monthFormatter () {
      return this.$refs.calendar.getFormatter({
        timeZone: 'UTC', month: 'long',
      })
    },
  },
  mounted () {
    this.$refs.calendar.checkChange()
    console.log(this.events);
  },
    watch: {
    loader () {
      const l = this.loader
      this[l] = !this[l]
    },
    'hours': function(){
      if(this.form.is == 'create'){
        this.form.queen_datetime = this.dateTimeString + ' ' + this.hours;
      }
    }
  },
  created(){
    
  },
  methods: {

    getEventsData(){
        const events = []
        const API = "http://127.0.0.1:8000/api/";

        let color = this.colors[this.rnd(0, this.colors.length - 1)]; 

        axios.get(API  + 'dates')
        .then(function (response) {
          response.data.available.forEach(function (item) {
            let data = {
                id: item.id,
                name: item.name,
                email: item.email,
                start: item.start,
                end: item.end,
                color: color,
            }
            events.push(data);
          });
        })
        .catch(function (error) {
          console.log(error);
        });

        this.events = events
    },
    showPicker(){
      let vm = this;

        vm.picker = true;
    },
      validate() {
        let vm = this;
        if (vm.$refs.form.validate()) {
            vm.loading = true;

            if(vm.form.is == 'update'){
              vm.updateAppointment();
            } else {
              vm.storeAppointment();
            }
        }
      },
      updateAppointment(){
        let vm = this;

        const events = []
        const API = "http://127.0.0.1:8000/api/";
        
        let config = {
          headers: { 'Content-Type': 'application/json' },
          responseType: 'json'
        };

        axios.post(API + 'update' + '/' + vm.form.id, vm.form, config)
        .then(function (response) {
          vm.loading = false;
          vm.dialog = false;
           vm.showSnackBar('success', response.data.msg);
        })
        .catch(function (error) {
          vm.dialog = true;
          vm.loading = false;
          vm.showSnackBar('error', error.response.data.msg);
        });

        this.events = events
      },
      storeAppointment(){
        let vm = this;

        const events = []
        const API = "http://127.0.0.1:8000/api/";
        // let color = this.colors[this.rnd(0, this.colors.length - 1)];
        
        let config = {
          headers: { 'Content-Type': 'application/json' },
          responseType: 'json'
        };

        axios.post(API + 'store', vm.form, config)
        .then(function (response) {

          console.log('store data', response.data);
          
          vm.showSnackBar('success', response.data.msg);
          vm.loading = false;
          vm.dialog = true;
        })
        .catch(function (error) {
          vm.dialog = true;
          vm.loading = false;
          vm.showSnackBar('error', error.response.data.msg);
        });

        this.events = events

      },
      showSnackBar(color, message){
        let vm = this;
        vm.$emit("showSnackBar", {
          color: color,
          snackbar: true,
          timeout: 8000,
          text: message,
        });
      },
    viewDay ({ date }) {
      this.focus = date
      this.type = 'day'
    },
    getEventColor (event) {
      return event.color
    },
    setToday () {
      this.focus = this.today
    },
    prev () {
      this.$refs.calendar.prev()
    },
    next () {
      this.$refs.calendar.next()
    },
    showEvent ({ nativeEvent, event }) {
      const open = () => {
        this.selectedEvent = event
        this.selectedElement = nativeEvent.target
        setTimeout(() => this.selectedOpen = true, 10)
      }

      if (this.selectedOpen) {
        this.selectedOpen = false
        setTimeout(open, 10)
      } else {
        open()
      }

      nativeEvent.stopPropagation()
    },
    updateRange ({ start, end }) {
      const events = []
      const API = "http://127.0.0.1:8000/api/";

      let color = this.colors[this.rnd(0, this.colors.length - 1)]; 

      axios.get(API  + 'dates')
      .then(function (response) {
         response.data.available.forEach(function (item) {
           let data = {
              id: item.id,
              name: item.name,
              email: item.email,
              start: item.start,
              end: item.end,
              color: color,
           }
           events.push(data);
        });
      })
      .catch(function (error) {
        console.log(error);
      });

      this.start = start
      this.end = end
      this.events = events
    },
    nth (d) {
      return d > 3 && d < 21
        ? 'th'
        : ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'][d % 10]
    },
    rnd (a, b) {
      return Math.floor((b - a + 1) * Math.random()) + a
    },
    formatDate (a, withTime) {
      return withTime
        ? `${a.getFullYear()}-${a.getMonth() + 1}-${a.getDate()} ${a.getHours()}:${a.getMinutes()}`
        : `${a.getFullYear()}-${a.getMonth() + 1}-${a.getDate()}`
    },
    storeDate(data){
      let vm = this;
      vm.dialog = true
      vm.dateTimeString = data.date;
    },
    updateDate(data){
      let vm = this;
      vm.dialog = true;
      vm.form.id = data.id;
      vm.storeOrUpdate = 'Update';
      vm.form.is = 'update';
      vm.form.name = data.name;
      vm.form.email = data.email;
      vm.hours = this.replace(data.start);
      vm.picker = true;

      let d = new Date(data.start);
      let mm = d.getMonth() + 1;
      let dd = d.getDate();
      let yy = d.getFullYear();

      let myDateString = yy + '-' + mm + '-' + dd; //(US)
      vm.form.queen_datetime = myDateString + ' ' + this.replace(data.start);
    },
    replace(string){
      let createdDate = new Date(string);
      let time = createdDate.toLocaleTimeString().replace(/(.*)\D\d+/, '$1');

      return(time);
    },
    deleteDate(data){
        let vm = this;

        const API = "http://127.0.0.1:8000/api/";
        
        let config = {
          headers: { 'Content-Type': 'application/json' },
          responseType: 'json'
        };

        axios.post(API + 'destroy' + '/' + data.id, null, config)
        .then(function (response) {
          this.getEventsData();
            vm.dialogDelete = false;
           vm.showSnackBar('success', response.data.msg);
           window.reload();
        })
        .catch(function (error) {
          vm.dialogDelete = false;
          vm.showSnackBar('error', error.response.data.msg);
        });
    }
  },
}
  
</script>

<style lang="scss">
  $padding: 40px;
  $font: 'Raleway', sans-serif;

  .pa40{
      padding: $padding;
  }

  h1, h2, h3, h4, p, ul, li{
    font-family: $font;
  }
</style>
