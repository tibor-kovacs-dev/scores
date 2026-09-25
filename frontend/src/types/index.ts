export interface Team {
  id: number
  external_id: number
  name: string
  short_name: string
  tla?: string
  logo_url: string
}

export interface TeamDetail extends Team {
  games?: Game[]
}

export interface Game {
  id: number
  external_id: number
  competition_code?: string
  competition_name?: string
  home_team_id: number
  away_team_id: number
  score_home: number
  score_away: number
  status: string
  utc_date: string
  minute?: number
  home_team: Team
  away_team: Team
}

export interface StandingTableRow {
  id: number
  playedGames: number
  won: number
  draw: number
  lost: number
  goalsFor: number
  goalsAgainst: number
  goalDifference: number
  points: number
  team: {
    id: number
    name: string
    crest: string
    shortName?: string
  }
}

export interface StandingGroup {
  group: string
  table: StandingTableRow[]
}

export interface StandingsData {
  standings: StandingGroup[]
}

export interface Scorer {
  player: {
    id: number
    name: string
  }
  team: {
    id: number
    name: string
  }
  goals: number
}

export interface MatchUpdateEvent {
  game: Game
}