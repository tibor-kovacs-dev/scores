import type { Game } from '@/types/index'

export const demoGames: Game[] = [
  {
    id: 9991,
    external_id: 9991,
    home_team_id: 1,
    away_team_id: 2,
    score_home: 2,
    score_away: 1,
    status: 'IN_PLAY',
    utc_date: new Date(Date.now() + 1000 * 60 * 12).toISOString(),
    minute: 67,
    home_team: {
      id: 1,
      external_id: 1,
      name: 'Németország',
      short_name: 'GER',
      logo_url: 'https://crests.football-data.org/759.svg' 
    },
    away_team: {
      id: 2,
      external_id: 2,
      name: 'Franciaország',
      short_name: 'FRA',
      logo_url: 'https://crests.football-data.org/773.svg'
    }
  },
  {
    id: 9992,
    external_id: 9992,
    home_team_id: 3,
    away_team_id: 4,
    score_home: 3,
    score_away: 0,
    status: 'FINISHED',
    utc_date: new Date(Date.now() - 1000 * 60 * 45).toISOString(),
    minute: undefined,
    home_team: {
      id: 3,
      external_id: 3,
      name: 'Argentína',
      short_name: 'ARG',
      logo_url: 'https://crests.football-data.org/762.svg'
    },
    away_team: {
      id: 4,
      external_id: 4,
      name: 'Brazília',
      short_name: 'BRA',
      logo_url: 'https://crests.football-data.org/764.svg'
    }
  },
  {
    id: 9993,
    external_id: 9993,
    home_team_id: 5,
    away_team_id: 6,
    score_home: 0,
    score_away: 0,
    status: 'TIMED',
    utc_date: new Date(Date.now() + 1000 * 60 * 75).toISOString(),
    minute: undefined,
    home_team: {
      id: 5,
      external_id: 5,
      name: 'Spanyolország',
      short_name: 'ESP',
      logo_url: 'https://crests.football-data.org/760.svg'
    },
    away_team: {
      id: 6,
      external_id: 6,
      name: 'Anglia',
      short_name: 'ENG',
      logo_url: 'https://crests.football-data.org/770.svg'
    }
  },
  {
    id: 9994,
    external_id: 9994,
    home_team_id: 7,
    away_team_id: 8,
    score_home: 1,
    score_away: 1,
    status: 'HALF_TIME',
    utc_date: new Date(Date.now() + 1000 * 60 * 5).toISOString(),
    minute: 45,
    home_team: {
      id: 7,
      external_id: 7,
      name: 'Olaszország',
      short_name: 'ITA',
      logo_url: 'https://crests.football-data.org/784.svg'
    },
    away_team: {
      id: 8,
      external_id: 8,
      name: 'Hollandia',
      short_name: 'NED',
      logo_url: 'https://crests.football-data.org/8601.svg'
    }
  }
]