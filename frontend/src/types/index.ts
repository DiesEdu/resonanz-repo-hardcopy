export interface SheetMusic {
  id: number;
  title: string;
  subtitle: string;
  composer: string;
  arranger: string;
  genre: string;
  difficulty: "Beginner" | "Intermediate" | "Advanced";
  pages: number;
  description: string;
  location_file: string;
  coverImage: string;
  instruments: string[];
  score_type: string;
}

export type UserRole = "admin" | "librarian" | "member";

export interface User {
  id: number;
  name: string | null;
  email: string;
  role: UserRole;
  created_at: string;
  updated_at: string;
}

export interface Genre {
  id: number;
  name: string;
  count: number;
}
