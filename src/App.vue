<script setup lang="ts">
import { computed, nextTick, onMounted, reactive, ref } from 'vue'
import {
  API_BASE,
  createBrand,
  createCar,
  deleteBrand as removeBrand,
  deleteCar as removeCar,
  fetchBrands,
  fetchCars,
  updateBrand,
  updateCar,
  type Brand,
  type BrandForm,
  type Car,
  type CarForm,
} from './api/sportCars'

const emptyCar = (): CarForm => ({
  model: '',
  price: 0,
  horsepower: 0,
  releaseYear: new Date().getFullYear(),
  brandId: brands.value[0]?.id ?? 0,
  imageUrl: '',
})

const emptyBrand = (): BrandForm => ({
  name: '',
  founded: 2000,
  country: '',
})

const brands = ref<Brand[]>([])
const cars = ref<Car[]>([])
const loading = ref(true)
const error = ref('')
const apiUrlLabel = API_BASE

const activeView = ref<'cars' | 'brands'>('cars')
const carQuery = ref('')
const brandFilter = ref('all')
const sortMode = ref<'model' | 'price' | 'horsepower' | 'year'>('model')
const editingCarId = ref<number | null>(null)
const editingBrandId = ref<number | null>(null)
const carWorkspace = ref<HTMLElement | null>(null)
const brandWorkspace = ref<HTMLElement | null>(null)
const carForm = reactive<CarForm>(emptyCar())
const brandForm = reactive<BrandForm>(emptyBrand())

const loadData = async () => {
  loading.value = true
  error.value = ''

  try {
    const [brandData, carData] = await Promise.all([fetchBrands(), fetchCars()])
    brands.value = brandData
    cars.value = carData

    if (!carForm.brandId && brandData[0]) {
      carForm.brandId = brandData[0].id
    }
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Impossible de charger les données.'
  } finally {
    loading.value = false
  }
}

onMounted(loadData)

const formatCurrency = (value: number) =>
  new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR',
    maximumFractionDigits: 0,
  }).format(value)

const getBrand = (brandId: number) => brands.value.find((brand) => brand.id === brandId)

const filteredCars = computed(() => {
  const query = carQuery.value.trim().toLowerCase()

  return cars.value
    .filter((car) => {
      const brand = getBrand(car.brandId)
      const matchesSearch =
        car.model.toLowerCase().includes(query) || brand?.name.toLowerCase().includes(query)
      const matchesBrand = brandFilter.value === 'all' || String(car.brandId) === brandFilter.value

      return matchesSearch && matchesBrand
    })
    .sort((first, second) => {
      if (sortMode.value === 'price') {
        return second.price - first.price
      }

      if (sortMode.value === 'horsepower') {
        return second.horsepower - first.horsepower
      }

      if (sortMode.value === 'year') {
        return second.releaseYear - first.releaseYear
      }

      return first.model.localeCompare(second.model)
    })
})

const totalValue = computed(() => cars.value.reduce((sum, car) => sum + car.price, 0))
const averagePower = computed(() => {
  if (!cars.value.length) {
    return 0
  }

  return Math.round(cars.value.reduce((sum, car) => sum + car.horsepower, 0) / cars.value.length)
})

const resetCarForm = () => {
  Object.assign(carForm, emptyCar())
  editingCarId.value = null
}

const resetBrandForm = () => {
  Object.assign(brandForm, emptyBrand())
  editingBrandId.value = null
}

const scrollToWorkspace = async (workspace: typeof carWorkspace) => {
  await nextTick()

  if (!workspace.value) {
    return
  }

  let top = 0
  let element: HTMLElement | null = workspace.value

  while (element) {
    top += element.offsetTop
    element = element.offsetParent as HTMLElement | null
  }

  window.scrollTo({ top: Math.max(0, top - 16), behavior: 'auto' })
}

const saveCar = async () => {
  if (!carForm.model.trim() || !carForm.brandId) {
    return
  }

  const carPayload: CarForm = {
    model: carForm.model.trim(),
    price: Number(carForm.price),
    horsepower: Number(carForm.horsepower),
    releaseYear: Number(carForm.releaseYear),
    brandId: Number(carForm.brandId),
    imageUrl: carForm.imageUrl.trim(),
  }

  try {
    error.value = ''

    if (editingCarId.value) {
      const updated = await updateCar(editingCarId.value, carPayload)
      cars.value = cars.value.map((car) => (car.id === updated.id ? updated : car))
    } else {
      const created = await createCar(carPayload)
      cars.value = [created, ...cars.value]
    }

    resetCarForm()
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Erreur lors de la sauvegarde.'
  }
}

const editCar = async (car: Car) => {
  Object.assign(carForm, car)
  editingCarId.value = car.id
  activeView.value = 'cars'
  await scrollToWorkspace(carWorkspace)
}

const deleteCar = async (carId: number) => {
  try {
    error.value = ''
    await removeCar(carId)
    cars.value = cars.value.filter((car) => car.id !== carId)

    if (editingCarId.value === carId) {
      resetCarForm()
    }
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Erreur lors de la suppression.'
  }
}

const saveBrand = async () => {
  if (!brandForm.name.trim() || !brandForm.country.trim()) {
    return
  }

  const brandPayload: BrandForm = {
    name: brandForm.name.trim(),
    founded: Number(brandForm.founded),
    country: brandForm.country.trim(),
  }

  try {
    error.value = ''

    if (editingBrandId.value) {
      const updated = await updateBrand(editingBrandId.value, brandPayload)
      brands.value = brands.value.map((brand) => (brand.id === updated.id ? updated : brand))
    } else {
      const created = await createBrand(brandPayload)
      brands.value = [...brands.value, created]

      if (!carForm.brandId) {
        carForm.brandId = created.id
      }
    }

    resetBrandForm()
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Erreur lors de la sauvegarde.'
  }
}

const editBrand = async (brand: Brand) => {
  Object.assign(brandForm, brand)
  editingBrandId.value = brand.id
  activeView.value = 'brands'
  await scrollToWorkspace(brandWorkspace)
}

const deleteBrand = async (brandId: number) => {
  const isUsed = cars.value.some((car) => car.brandId === brandId)

  if (isUsed) {
    alert('Cette marque est encore utilisée par une voiture.')
    return
  }

  try {
    error.value = ''
    await removeBrand(brandId)
    brands.value = brands.value.filter((brand) => brand.id !== brandId)

    if (editingBrandId.value === brandId) {
      resetBrandForm()
    }
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Erreur lors de la suppression.'
  }
}

const resetDemoData = async () => {
  carQuery.value = ''
  brandFilter.value = 'all'
  resetCarForm()
  resetBrandForm()
  await loadData()
}
</script>

<template>
  <div class="app-shell">
    <header class="topbar">
      <div>
        <p class="eyebrow">WR406D - TP Vue.js</p>
        <h1>Gestion des véhicules de sport</h1>
      </div>
      <button class="ghost-button" type="button" :disabled="loading" @click="resetDemoData">
        Recharger les données
      </button>
    </header>

    <p v-if="error" class="api-error">{{ error }}</p>
    <p v-if="loading" class="api-loading">Chargement depuis l'API...</p>

    <main>
      <section class="hero">
        <div class="hero-copy">
          <p class="eyebrow">Connecté à Symfony / API Platform</p>
          <h2>CRUD en temps réel sur l'API Docker.</h2>
          <p>
            Les marques et voitures sont lues et modifiées via
            <code>{{ apiUrlLabel }}</code>.
          </p>
        </div>
        <div class="hero-media" aria-label="Voiture de sport">
          <img
            src="https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?auto=format&fit=crop&w=1200&q=80"
            alt="Voiture de sport rouge"
          />
        </div>
      </section>

      <section class="stats-grid" aria-label="Statistiques">
        <article>
          <span>{{ cars.length }}</span>
          <p>véhicules</p>
        </article>
        <article>
          <span>{{ brands.length }}</span>
          <p>marques</p>
        </article>
        <article>
          <span>{{ averagePower }} ch</span>
          <p>puissance moyenne</p>
        </article>
        <article>
          <span>{{ formatCurrency(totalValue) }}</span>
          <p>valeur catalogue</p>
        </article>
      </section>

      <nav class="tabs" aria-label="Navigation principale">
        <button
          type="button"
          :class="{ active: activeView === 'cars' }"
          @click="activeView = 'cars'"
        >
          Voitures
        </button>
        <button
          type="button"
          :class="{ active: activeView === 'brands' }"
          @click="activeView = 'brands'"
        >
          Marques
        </button>
      </nav>

      <section v-if="activeView === 'cars'" ref="carWorkspace" class="workspace">
        <form class="editor-panel" @submit.prevent="saveCar">
          <div class="panel-heading">
            <p class="eyebrow">{{ editingCarId ? 'Modification' : 'Création' }}</p>
            <h2>{{ editingCarId ? 'Modifier une voiture' : 'Ajouter une voiture' }}</h2>
          </div>

          <label>
            Modèle
            <input v-model="carForm.model" type="text" placeholder="Ex: Alpine A110 R" required />
          </label>

          <div class="field-row">
            <label>
              Prix
              <input v-model.number="carForm.price" type="number" min="0" step="1000" required />
            </label>
            <label>
              Puissance
              <input v-model.number="carForm.horsepower" type="number" min="0" required />
            </label>
          </div>

          <div class="field-row">
            <label>
              Année
              <input v-model.number="carForm.releaseYear" type="number" min="1900" required />
            </label>
            <label>
              Marque
              <select v-model.number="carForm.brandId" required>
                <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                  {{ brand.name }}
                </option>
              </select>
            </label>
          </div>

          <label>
            Photo
            <input v-model="carForm.imageUrl" type="url" placeholder="https://..." />
          </label>

          <div class="form-actions">
            <button class="primary-button" type="submit">
              {{ editingCarId ? 'Enregistrer' : 'Ajouter' }}
            </button>
            <button v-if="editingCarId" class="ghost-button" type="button" @click="resetCarForm">
              Annuler
            </button>
          </div>
        </form>

        <div class="list-panel">
          <div class="toolbar">
            <label>
              Rechercher
              <input v-model="carQuery" type="search" placeholder="Modèle ou marque" />
            </label>
            <label>
              Marque
              <select v-model="brandFilter">
                <option value="all">Toutes</option>
                <option v-for="brand in brands" :key="brand.id" :value="String(brand.id)">
                  {{ brand.name }}
                </option>
              </select>
            </label>
            <label>
              Trier
              <select v-model="sortMode">
                <option value="model">Modèle</option>
                <option value="price">Prix décroissant</option>
                <option value="horsepower">Puissance</option>
                <option value="year">Année</option>
              </select>
            </label>
          </div>

          <div class="car-grid">
            <article v-for="car in filteredCars" :key="car.id" class="car-card">
              <img
                :src="
                  car.imageUrl ||
                  'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=900&q=80'
                "
                :alt="car.model"
              />
              <div class="car-card-body">
                <div>
                  <p class="brand-name">{{ getBrand(car.brandId)?.name ?? 'Marque inconnue' }}</p>
                  <h3>{{ car.model }}</h3>
                </div>
                <dl>
                  <div>
                    <dt>Prix</dt>
                    <dd>{{ formatCurrency(car.price) }}</dd>
                  </div>
                  <div>
                    <dt>Puissance</dt>
                    <dd>{{ car.horsepower }} ch</dd>
                  </div>
                  <div>
                    <dt>Sortie</dt>
                    <dd>{{ car.releaseYear }}</dd>
                  </div>
                </dl>
                <div class="card-actions">
                  <button class="ghost-button" type="button" @click="editCar(car)">Modifier</button>
                  <button class="danger-button" type="button" @click="deleteCar(car.id)">
                    Supprimer
                  </button>
                </div>
              </div>
            </article>
          </div>

          <p v-if="!filteredCars.length" class="empty-state">Aucune voiture ne correspond.</p>
        </div>
      </section>

      <section v-else ref="brandWorkspace" class="workspace">
        <form class="editor-panel" @submit.prevent="saveBrand">
          <div class="panel-heading">
            <p class="eyebrow">{{ editingBrandId ? 'Modification' : 'Création' }}</p>
            <h2>{{ editingBrandId ? 'Modifier une marque' : 'Ajouter une marque' }}</h2>
          </div>

          <label>
            Nom
            <input v-model="brandForm.name" type="text" placeholder="Ex: Lamborghini" required />
          </label>
          <label>
            Année de création
            <input v-model.number="brandForm.founded" type="number" min="1800" required />
          </label>
          <label>
            Pays d'origine
            <input v-model="brandForm.country" type="text" placeholder="Ex: Italie" required />
          </label>

          <div class="form-actions">
            <button class="primary-button" type="submit">
              {{ editingBrandId ? 'Enregistrer' : 'Ajouter' }}
            </button>
            <button v-if="editingBrandId" class="ghost-button" type="button" @click="resetBrandForm">
              Annuler
            </button>
          </div>
        </form>

        <div class="table-panel">
          <table>
            <thead>
              <tr>
                <th>Marque</th>
                <th>Création</th>
                <th>Pays</th>
                <th>Véhicules</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="brand in brands" :key="brand.id">
                <td>{{ brand.name }}</td>
                <td>{{ brand.founded }}</td>
                <td>{{ brand.country }}</td>
                <td>{{ cars.filter((car) => car.brandId === brand.id).length }}</td>
                <td>
                  <div class="row-actions">
                    <button class="ghost-button" type="button" @click="editBrand(brand)">
                      Modifier
                    </button>
                    <button class="danger-button" type="button" @click="deleteBrand(brand.id)">
                      Supprimer
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </main>
  </div>
</template>
