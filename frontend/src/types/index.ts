export interface SheetMusic {
  id: number;
  title: string;
  composer: string;
  genre: string;
  difficulty: "Beginner" | "Intermediate" | "Advanced";
  pages: number;
  description: string;
  location_file: string;
  coverImage: string;
  fileUrl: string;
  uploadDate: string;
  instruments: string[];
}

export interface Genre {
  id: number;
  name: string;
  count: number;
}
