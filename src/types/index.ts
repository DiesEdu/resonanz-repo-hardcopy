export interface SheetMusic {
  id: number
  title: string
  composer: string
  genre: string
  difficulty: 'Beginner' | 'Intermediate' | 'Advanced'
  pages: number
  description: string
  coverImage: string
  fileUrl: string
  uploadDate: string
  instrumentation: string[]
}

export interface Genre {
  id: number
  name: string
  count: number
}
