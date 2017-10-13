<template>
    <div class="container is-fluid">
        <nav class="breadcrumb" aria-label="breadcrumbs" id="breadcrumbs-container">
            <ul>
                <li class="is-active"><a href="/players">Leaderboard</a></li>
            </ul>
        </nav>
                <section class="panel">
                    <p class="panel-heading">
                        Filters
                    </p>
                    <div class="panel-block">
                        <form class="control">
                            <div class="field">
                                <label class="label">Group</label>
                                <div class="control">
                                    <div class="select" >
                                        <select v-on:change="submit()" v-model="group_value">
                                            <option :value="0">
                                                All Players
                                            </option>
                                            <option :value="group.id"  v-for="group in groups">
                                                {{group.name}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" style="padding-top:7px;">Season</label>
                                <div class="control">
                                    <div class="select">
                                        <select v-on:change="submit()" v-model="season">
                                            <option value="0">All years</option>
                                            <option value="1">Season 0</option>
                                            <option value="2">Season 1</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" style="padding-top:7px;">Minimum Plays</label>
                                <div class="control">
                                    <input class="input" type="text" v-model="min_plays"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
                <div>
                    <h1> Leaderboard</h1>
                    <div   v-bind:class="{ 'is-loading' : loading}" v-if="group_value" style="height:500px">
                            <line-chart :chart-data="chart_data" :options="{
                                    elements: {
                                        line: {
                                            tension: 0
                                        }
                                    },
                                    scales: {
                                        xAxes: [{
                                        type: 'time',
                                        scaleLabel: {
                                        labelString: 'Date'
                                    }
                                }],
                        }}">
                            </line-chart>
                    </div>
                </div>
                <div>
                    <b-table
                            :data="filtered_player_list"
                            :striped="true"
                            :loading="loading">
                        <template scope="props">
                            <b-table-column label="Place" numeric>{{ props.index + 1}}</b-table-column>
                            <b-table-column label="Name">{{ props.row.name }}</b-table-column>
                            <b-table-column label="Plays">{{ props.row.play_count }}</b-table-column>
                            <b-table-column label="Wins">{{ props.row.wins }}</b-table-column>
                            <b-table-column label="Win Rate">{{ props.row.win_rate | percent }}%</b-table-column>
                            <b-table-column label="Adjusted Plays">{{ props.row.new_play_count | round }}</b-table-column>
                            <b-table-column label="Adjusted Wins">{{ props.row.new_wins | round }}</b-table-column>
                            <b-table-column label="Adjusted Win Rate">{{ props.row.new_win_rate | percent }}%</b-table-column>
                            <b-table-column label="Expected Win Rate">{{ props.row.new_expected_win_rate | percent}}%</b-table-column>
                            <b-table-column label="POE">{{ props.row.new_adjusted_win_rate | percent}}%</b-table-column>

                        </template>
                    </b-table>
                </div>

        </div>
    </div>
</template>

<script>
    import LineChart from './LineChart.js'

    export default {
        components: {
            LineChart
        },
        data() {
            return {players: [], groups: [], group_value: 0, season: 0, min_plays: 5, loading:true, chart_data: null}
        },
        filters: {
            percent: function (value) {
                var factor = Math.pow(10, 2);
                var tempNumber = value * 100 * factor;
                var roundedTempNumber = Math.round(tempNumber);
                return roundedTempNumber / factor;
            },
            round: function (value) {
                var factor = Math.pow(10, 2);
                var tempNumber = value * factor;
                var roundedTempNumber = Math.round(tempNumber);
                return roundedTempNumber / factor;
            }
        },
        computed: {
            filtered_player_list: function() {
                return this.players.filter(
                    (player) => {
                        //TODO have filter only accept numbers
                        return player.play_count >= this.min_plays
                    }
                );
            }
        },

        created() {
            axios.post('/players/json', {
                'group': this.group_value,
                'season': this.season
            }).then(response => {

                    this.loading=false;
                    this.players = response.data.players;
                    this.groups = response.data.groups;
                    this.group_value = response.data.group;
                    this.season = response.data.season;
                }
            );
        },

        methods: {
            submit() {
                console.log("posted", this.season, this.group_value);
                this.loading=true;

                axios.post('/players/json', {
                    'group': this.group_value,
                    'season': this.season
                }).then(response => {

                    this.loading=false;
                        console.log("receive", response.data);

                        //map chart data to moment format

                    var player_chart_data = response.data.players.map((player) => {
                        player.chart_data.map((entry) => {
                            entry.x = moment(entry.x).toDate();
                            return entry;
                        });

                        return {label: player.name,
                                borderColor:"#"+((1<<24)*Math.random()|0).toString(16),
                                fill: false,
                                data: player.chart_data
                        };
                    });

                    response.data.players.map((player) => {
                        player.chart_data.map((entry) => {
                            entry.x = moment(entry.x).toDate();
                            return entry;
                        });
                    })
                    this.chart_data = {
                        datasets: player_chart_data
                    };
                    this.players = response.data.players;
                        this.groups = response.data.groups;
                        this.group_value = response.data.group;
                        this.season = response.data.season;

                    }
                );
            }
        }


    }
</script>