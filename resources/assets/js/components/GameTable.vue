
<template>
    <div class="container is-fluid">
        <nav class="breadcrumb" aria-label="breadcrumbs" id="breadcrumbs-container">
            <ul>
                <li><a href="/games">Games</a></li>
                <li class="is-active"><a href="#">{{game.name}}</a></li>
            </ul>
        </nav>
        <div class="columns">
            <div class="column is-one-quarter">
                <section class="panel">
                    <p class="panel-heading">
                        Filters
                    </p>
                    <div class="panel-block">
                        <form class="control">
                            <field name="group-field">
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
                            </field>
                            <field name="season-field">
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
                            </field>
                            <div class="field">
                                <label class="label" style="padding-top:7px;">Minimum Plays</label>
                                <div class="control">
                                    <input class="input" type="text" v-model="min_plays"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>

            <div class="column">
                <section class="panel">
                    <p class="panel-heading" v-if="player">{{game.name}} Leaderboard</p>
                    <div class="panel-block leaderboard-panel-body">
                        <table class="table">
                            <thead>
                            <th>Game</th>
                            <th>Plays</th>
                            <th>Wins</th>
                            <th>Win Rate</th>

                            </thead>
                            <tr v-for="player in filtered_player_list">
                                <td>{{player.name}}</td>
                                <td>{{player.play_count}}</td>
                                <td>{{player.wins}}</td>
                                <td>{{player.win_rate | percent}} %  </td>

                            </tr>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props:['id','initialGroup','initialSeason'],
        data() {

            return {players: [], groups: [], group_value: this.initialGroup || 0, season: this.initialSeason || 0, game: 0, min_plays: 0}
        },
        filters: {
            percent: function (value) {
                var factor = Math.pow(10, 2);
                var tempNumber = value * 100 * factor;
                var roundedTempNumber = Math.round(tempNumber);
                return roundedTempNumber / factor ;
            },
            round: function (value) {
                var factor = Math.pow(10, 2);
                var tempNumber = value  * factor;
                var roundedTempNumber = Math.round(tempNumber);
                return roundedTempNumber / factor ;
            }
        },

        computed: {
            filtered_player_list: function() {
                return this.players.filter(
                    (player) => {
                        //TODO have filter only accept numbers
                        return player.play_count >= this.min_plays;
                    }
                );
            }
        },


        created() {
            axios.post('/games/' + this.id + '/json',{
                'group' : this.group_value,
                'season' : this.season
            }).then(response => {
                    this.players = response.data.players;
                    this.groups = response.data.groups;
                    this.group_value = response.data.group;
                    this.season = response.data.season;
                    this.game = response.data.game
                }
            );
        },

        methods: {
            submit() {
                axios.post('/games/' + this.id + '/json',{
                    'group' : this.group_value,
                    'season' : this.season
                }).then(response => {
                        this.players = response.data.players;
                        this.groups = response.data.groups;
                        this.group_value = response.data.group;
                        this.season = response.data.season;
                        this.game = response.data.game
                    }
                );
            }
        }


    }
</script>