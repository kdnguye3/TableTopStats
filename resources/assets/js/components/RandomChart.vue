<template>
    <div>
        <line-chart :chart-data="datacollection" :options="{scales: {
            xAxes: [{
						type: 'time',
                        scaleLabel: {
                        labelString: 'Date'
                        }
                    }],
        }}"></line-chart>
        <button @click="fillData()">Randomize</button>
    </div>
</template>

<script>
    import LineChart from './LineChart.js'
    export default {
        components: {
            LineChart
        },
        data () {
            return {
                datacollection: null
            }
        },
        mounted () {
            this.fillData()
        },
        methods: {
            fillData () {
                this.datacollection = {
                    datasets: [
                        {
                            label: 'Data One',
                            borderColor: '#f87979',
                            data: [{x:moment().subtract(50, 'days').toDate(), y:10},{x:moment().toDate(), y:20}]
                        }, {
                            label: 'Data Two',
                            borderColor: 'black',
                            data: [{x:moment().subtract(5, 'days').toDate(), y:20},{x:moment().subtract(3, 'days').toDate(), y:30}]
                        }
                    ]
                }
            },
            getRandomInt () {
                return Math.floor(Math.random() * (50 - 5 + 1)) + 5
            }
        }
    }
</script>

<style>
    .small {
        max-width: 600px;
        margin:  150px auto;
    }
</style>