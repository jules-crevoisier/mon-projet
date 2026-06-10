import type { ApiMarque, ApiVoiture } from '../types/api'

export type Brand = {
  id: number
  name: string
  founded: number
  country: string
}

export type Car = {
  id: number
  model: string
  price: number
  horsepower: number
  releaseYear: number
  brandId: number
  imageUrl: string
}

export type CarForm = Omit<Car, 'id'>
export type BrandForm = Omit<Brand, 'id'>

type ApiCollection<T> = T[] | { member?: T[]; 'hydra:member'?: T[] }

export const API_BASE = (import.meta.env.VITE_API_URL ?? '/api').replace(/\/$/, '')

const headers = {
  Accept: 'application/json',
  'Content-Type': 'application/json',
}

async function request<T>(path: string, init?: RequestInit): Promise<T> {
  const response = await fetch(`${API_BASE}${path}`, {
    ...init,
    headers: { ...headers, ...init?.headers },
  })

  if (!response.ok) {
    const message = await response.text()
    throw new Error(message || `Erreur API (${response.status})`)
  }

  if (response.status === 204) {
    return undefined as T
  }

  return response.json() as Promise<T>
}

const marqueIdFromVoiture = (marque: ApiVoiture['marque']): number => {
  if (typeof marque === 'string') {
    const id = marque.split('/').pop()
    return Number(id)
  }

  return marque.id
}

const collectionItems = <T>(data: ApiCollection<T>): T[] => {
  if (Array.isArray(data)) {
    return data
  }

  return data.member ?? data['hydra:member'] ?? []
}

export const mapMarque = (marque: ApiMarque): Brand => ({
  id: marque.id,
  name: marque.nom,
  founded: marque.anneeCreation,
  country: marque.paysOrigine,
})

export const mapVoiture = (voiture: ApiVoiture): Car => ({
  id: voiture.id,
  model: voiture.modele,
  price: Number(voiture.prix),
  horsepower: voiture.puissance,
  releaseYear: voiture.anneeSortie,
  brandId: marqueIdFromVoiture(voiture.marque),
  imageUrl: voiture.photo ?? '',
})

export async function fetchBrands(): Promise<Brand[]> {
  const data = await request<ApiCollection<ApiMarque>>('/marques')
  return collectionItems(data).map(mapMarque)
}

export async function fetchCars(): Promise<Car[]> {
  const data = await request<ApiCollection<ApiVoiture>>('/voitures')
  return collectionItems(data).map(mapVoiture)
}

export async function createBrand(form: BrandForm): Promise<Brand> {
  const data = await request<ApiMarque>('/marques', {
    method: 'POST',
    body: JSON.stringify({
      nom: form.name,
      anneeCreation: form.founded,
      paysOrigine: form.country,
    }),
  })

  return mapMarque(data)
}

export async function updateBrand(id: number, form: BrandForm): Promise<Brand> {
  const data = await request<ApiMarque>(`/marques/${id}`, {
    method: 'PUT',
    body: JSON.stringify({
      nom: form.name,
      anneeCreation: form.founded,
      paysOrigine: form.country,
    }),
  })

  return mapMarque(data)
}

export async function deleteBrand(id: number): Promise<void> {
  await request<void>(`/marques/${id}`, { method: 'DELETE' })
}

export async function createCar(form: CarForm): Promise<Car> {
  const data = await request<ApiVoiture>('/voitures', {
    method: 'POST',
    body: JSON.stringify({
      modele: form.model,
      prix: String(form.price),
      puissance: form.horsepower,
      anneeSortie: form.releaseYear,
      photo: form.imageUrl || null,
      marque: `/api/marques/${form.brandId}`,
    }),
  })

  return mapVoiture(data)
}

export async function updateCar(id: number, form: CarForm): Promise<Car> {
  const data = await request<ApiVoiture>(`/voitures/${id}`, {
    method: 'PUT',
    body: JSON.stringify({
      modele: form.model,
      prix: String(form.price),
      puissance: form.horsepower,
      anneeSortie: form.releaseYear,
      photo: form.imageUrl || null,
      marque: `/api/marques/${form.brandId}`,
    }),
  })

  return mapVoiture(data)
}

export async function deleteCar(id: number): Promise<void> {
  await request<void>(`/voitures/${id}`, { method: 'DELETE' })
}
