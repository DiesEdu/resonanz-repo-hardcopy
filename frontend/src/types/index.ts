export interface SheetMusic {
  id: number;
  title: string;
  subtitle: string;
  composer: string;
  genre: string;
  difficulty: "Beginner" | "Intermediate" | "Advanced";
  pages: number;
  description: string;
  location_file: string;
  coverImage: string;
  instruments: string[];
  score_type: string;
}

export interface Genre {
  id: number;
  name: string;
  count: number;
}
