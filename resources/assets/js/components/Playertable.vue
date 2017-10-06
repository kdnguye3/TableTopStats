
<template>
    <div class="container is-fluid">
        <nav class="breadcrumb" aria-label="breadcrumbs" id="breadcrumbs-container">
            <ul>
                <li><a href="/players">Leaderboard</a></li>
                <li class="is-active"><a href="#">Player</a></li>
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
                        </form>
                    </div>
                </section>
            </div>

            <div class="column">
                <section class="panel">
                    <p class="panel-heading" v-if="player">{{player.name}}'s plays</p>
                    <div class="panel-block table-panel-body"  v-bind:class="{ 'is-loading' : loading}">
                        <table class="table" >
                            <thead>
                                <th>Game</th>
                                <th>Plays</th>
                                <th>Wins</th>
                                <th>Win Rate</th>
                                <th>Adjusted Plays</th>
                                <th>Adjusted Wins</th>
                            </thead>
                            <tr v-for="game in plays">
                                <td>{{game.name}}</td>
                                <td>{{game.play_count}}</td>
                                <td>{{game.wins}}</td>
                                <td>{{game.win_rate | percent }}%</td>
                                <td>{{game.new_play_count | round}}</td>
                                <td>{{game.new_wins | round}}</td>
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

            return {plays: [], groups: [], group_value: this.initialGroup || 0, season: this.initialSeason || 0, player: 0,  loading:true}
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


        created() {
            axios.post('/players/' + this.id + '/json',{
                'group' : this.group_value,
                'season' : this.season
            }).then(response => {
                    this.loading = false;
                    this.plays = response.data.plays;
                    this.groups = response.data.groups;
                    this.group_value = response.data.group;
                    this.season = response.data.season;
                    this.player = response.data.player
                }
            );
        },

        methods: {
            submit() {
                this.loading=true;
                axios.post('/players/'+ this.id +'/json',{
                    'group' : this.group_value,
                    'season' : this.season
                }).then(response => {
                    this.loading = false;

                    this.plays = response.data.plays;
                    this.groups = response.data.groups;
                    this.group_value = response.data.group;
                    this.season = response.data.season;
                    this.player = response.data.player

                });
            }
        }


    }
</script>