<template>
    <div class="container">
        <h1 class="text-center">Todo list</h1>
        <p>Author: <a href="mailto:ductranxuan.29710@gmail.com">ductranxuan</a></p>
        <v-btn dark color="primary" href="/add">
            Click here to add new work
        </v-btn>
        <v-row class="fill-height">
            <v-col>
                <v-sheet height="64">
                    <v-toolbar flat color="white">
                        <v-btn outlined class="mr-4" color="grey darken-2" @click="setToday">
                            Today
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
                                <v-list-item @click="type = '4day'">
                                    <v-list-item-title>4 days</v-list-item-title>
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
                            :now="today"
                            :type="type"
                            @click:event="showEvent"
                            @click:more="viewDay"
                            @click:date="viewDay"
                            @change="updateRange"
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
                                <v-btn icon>
                                    <v-icon>mdi-pencil</v-icon>
                                </v-btn>
                                <v-toolbar-title>Edit: {{ selectedEvent.name }}</v-toolbar-title>
                            </v-toolbar>
                            <v-card-text>
                                <form method="post" action="/edit">
                                    <v-text-field
                                            v-model="name"
                                            :error-messages="nameErrors"
                                            :counter="20"
                                            label="Work name"
                                            required
                                            @input="$v.name.$touch()"
                                            @blur="$v.name.$touch()"
                                    ></v-text-field>
                                    <v-dialog
                                            ref="dialog"
                                            v-model="modalRange"
                                            :return-value.sync="dates"
                                            persistent
                                            width="290px"
                                    >
                                        <template v-slot:activator="{ on }">
                                            <v-text-field
                                                    v-model="dateRangeText" label="Date range" readonly v-on="on"
                                                    :error-messages="datesErrors"
                                                    @input="$v.dates.$touch()"
                                                    @blur="$v.dates.$touch()"
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker v-model="dates" range>
                                            <v-spacer></v-spacer>
                                            <v-btn text color="primary" @click="modalRange = false">Cancel</v-btn>
                                            <v-btn text color="primary" @click="$refs.dialog.save(dates)">OK</v-btn>
                                        </v-date-picker>
                                    </v-dialog>
                                    <v-btn class="mr-4" color="primary" @click="submit">submit</v-btn>
                                    <v-btn @click="clear">clear</v-btn>
                                </form>
                            </v-card-text>
                        </v-card>
                    </v-menu>
                </v-sheet>
            </v-col>
        </v-row>
    </div>
</template>

<script>
    import axios from 'axios';
    import {validationMixin} from 'vuelidate';
    import {required, maxLength, date} from 'vuelidate/lib/validators';

    export default {
        name: 'calendar',
        mixins: [validationMixin],
        mounted() {
            this.$refs.calendar.checkChange()
        },
        validations: {
            name: {required, maxLength: maxLength(20)},
            dates: {required},
        },
        computed: {
            dateRangeText () {
                return this.dates.join(' ~ ')
            },
            nameErrors() {
                const errors = []
                if (!this.$v.name.$dirty) return errors
                !this.$v.name.maxLength && errors.push('Name must be at most 10 characters long')
                !this.$v.name.required && errors.push('Name is required.')
                return errors
            },
            datesErrors() {
                const errors = []
                if (!this.$v.dates.$dirty) return errors
                !this.$v.dates.required && errors.push('Date range is required.')
                return errors
            },
            title() {
                const {start, end} = this
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
            monthFormatter() {
                return this.$refs.calendar.getFormatter({
                    timeZone: 'UTC', month: 'long',
                })
            },
        },
        data: () => ({
            id: null,
            dates: [],
            name: '',
            modalRange: false,
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
            today: null,
        }),
        methods: {
            viewDay({date}) {
                this.focus = date
                this.type = 'day'
            },
            getEventColor(event) {
                return event.color
            },
            setToday() {
                this.focus = this.today
            },
            prev() {
                this.$refs.calendar.prev()
            },
            next() {
                this.$refs.calendar.next()
            },
            showEvent({nativeEvent, event}) {
                const open = () => {
                    this.selectedEvent = event;
                    this.selectedElement = nativeEvent.target;

                    this.name = event.name;
                    this.dates = [event.start, event.end];
                    this.status = event.status;
                    this.id = event.id;

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
            updateRange({start, end}) {
                let events = [];
                this.start = start;
                this.end = end;
                axios.post('filter', {start: start.date, end: end.date})
                    .then(res => {
                        events = res.data;
                        this.events = events.map(value => {
                            switch(value.status) {
                                case '0':
                                    value.color = 'red';
                                    break;
                                case '1':
                                    value.color = 'orange';
                                    break;
                                case '2':
                                    value.color = 'cyan';
                                    break;
                                default:
                                // code block
                            }

                            return value;
                        })
                        console.log(this.events)

                    }).catch(() => {
                    this.$toasted.error('Loading error...')
                })
            },
            nth (d) {
                return d > 3 && d < 21
                    ? 'th'
                    : ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'][d % 10]
            },
            submit() {
                this.$v.$touch()
                if (this.$v.$pending || this.$v.$error) return;
                //Call api
                this.$toasted.show("Toasted !!", {
                    theme: "bubble",
                    position: "top-right",
                    duration : 5000
                });
                axios.post('/update', {
                    id: this.id,
                    name: this.name,
                    dates: this.dates
                }).then((res) => {
                    console.log(res)
                }).catch(err=> {
                    console.log(err)
                })
            },
            clear() {
                this.$v.$reset()
                this.name = ''
                this.dates = []
            },
        },
    }
</script>
