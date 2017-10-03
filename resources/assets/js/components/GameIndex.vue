<template>
    <div class="container is-fluid">
        <nav class="breadcrumb" aria-label="breadcrumbs" id="breadcrumbs-container">
            <ul>
                <li class="is-active"><a href="/games">Games</a></li>
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
                            <div class="field">
                                <label class="label" style="padding-top:7px;">Minimum Weight</label>
                                <div class="control">
                                    <input class="input" type="text" v-model="min_weight"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
            <div class="column">
                <section class="panel">
                    <p class="panel-heading">Games List</p>
                    <div class="panel-block leaderboard-panel-body">
                        <table class="table leaderboard">
                            <thead>
                                <th>Name</th>
                                <th>Plays</th>
                                <th>Complexity</th>
                            </thead>
                            <tr v-for="game in filtered_game_list">
                                <td><a :href="'/games/'+ game.id + '?group=' + group_value + '&season=' + season">{{game.name}}</a></td>
                                <td>{{game.play_count}}</td>
                                <td>{{game.weight}}</td>
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
        data() {
            return {games: [], groups: [], group_value: 0, season: 0, min_plays: 0, min_weight: 0}
        },
        computed: {
            filtered_game_list: function() {
                return this.games.filter(
                    (game) => {
                        //TODO have filter only accept numbers
                        return game.play_count >= this.min_plays && game.weight > this.min_weight;
                    }
                );
            }
        },

        created() {
            axios.post('/games/json', {
                'group': this.group_value,
                'season': this.season
            }).then(response => {
                    this.games = response.data.games;
                    this.groups = response.data.groups;
                    this.group_value = response.data.group;
                    this.season = response.data.season;
                }
            );
        },

        methods: {
            submit() {
                console.log("posted", this.season, this.group_value);
                axios.post('/games/json', {
                    'group': this.group_value,
                    'season': this.season
                }).then(response => {
                        console.log("receive", response.data);

                        this.games = response.data.games;
                        this.groups = response.data.groups;
                        this.group_value = response.data.group;
                        this.season = response.data.season;
                    }
                );
            }
        }


    }
</script>