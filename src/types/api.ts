export interface ApiMarque {
  id: number
  nom: string
  anneeCreation: number
  paysOrigine: string
  voitures?: ApiVoiture[]
}

export interface ApiVoiture {
  id: number
  modele: string
  prix: string
  puissance: number
  anneeSortie: number
  photo: string | null
  marque: ApiMarque | string
}
