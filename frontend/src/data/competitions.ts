export const competitions = [
  { code: 'PL', name: 'Premier League' },
  { code: 'PD', name: 'La Liga' },
  { code: 'SA', name: 'Serie A' },
  { code: 'BL1', name: 'Bundesliga' },
  { code: 'BL2', name: '2. Bundesliga' },
  { code: 'FL1', name: 'Ligue 1' },
  { code: 'DED', name: 'Eredivisie' },
  { code: 'PPL', name: 'Primeira Liga' },
  { code: 'ELC', name: 'Championship' },
  { code: 'BSA', name: 'Brasileirão Série A' },
  { code: 'CLI', name: 'Copa Libertadores' },
  { code: 'CL', name: 'Bajnokok Ligája' },
  { code: 'WC', name: 'World Cup' },
  { code: 'EC', name: 'European Championship' },
] as const

export type CompetitionCode = typeof competitions[number]['code']

export const leagueNames: Record<string, string> = Object.fromEntries(
  competitions.map(c => [c.code, c.name])
)

export const getLeagueName = (code: string): string => {
  return leagueNames[code] || code
}