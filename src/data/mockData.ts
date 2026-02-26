import type { SheetMusic, Genre } from "@/types";

export const mockSheetMusic: SheetMusic[] = [
  {
    id: 1,
    title: "Moonlight Sonata",
    composer: "Ludwig van Beethoven",
    genre: "Classical",
    difficulty: "Intermediate",
    pages: 6,
    description:
      "Piano Sonata No. 14, also known as the 'Moonlight Sonata', is one of Beethoven's most famous piano compositions. Perfect for intermediate pianists.",
    coverImage:
      "https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?auto=format&fit=crop&q=80&w=300&h=400",
    fileUrl: "/sheets/moonlight-sonata.pdf",
    uploadDate: "2024-01-15",
    instrumentation: ["Piano"],
  },
  {
    id: 2,
    title: "Clair de Lune",
    composer: "Claude Debussy",
    genre: "Impressionist",
    difficulty: "Advanced",
    pages: 4,
    description:
      "The third movement of Debussy's Suite Bergamasque, this beautiful piece requires delicate touch and expressive playing.",
    coverImage:
      "https://images.unsplash.com/photo-1465847899084-d164df4dedc6?auto=format&fit=crop&q=80&w=300&h=400",
    fileUrl: "/sheets/clair-de-lune.pdf",
    uploadDate: "2024-01-10",
    instrumentation: ["Piano"],
  },
  {
    id: 3,
    title: "Air on the G String",
    composer: "Johann Sebastian Bach",
    genre: "Baroque",
    difficulty: "Intermediate",
    pages: 3,
    description:
      "One of Bach's most beautiful melodies, arranged here for violin or string ensemble.",
    coverImage:
      "https://images.unsplash.com/photo-1507838153414-b4b713384a76?auto=format&fit=crop&q=80&w=300&h=400",
    fileUrl: "/sheets/air-on-g-string.pdf",
    uploadDate: "2024-01-05",
    instrumentation: ["Violin", "String Ensemble"],
  },
  {
    id: 4,
    title: "Gymnopédie No.1",
    composer: "Erik Satie",
    genre: "Modern",
    difficulty: "Beginner",
    pages: 2,
    description: "A gentle, atmospheric piece perfect for beginners. Simple yet profound.",
    coverImage:
      "https://images.unsplash.com/photo-1511379938547-c1f69419868d?auto=format&fit=crop&q=80&w=300&h=400",
    fileUrl: "/sheets/gymnopedie-no1.pdf",
    uploadDate: "2024-01-20",
    instrumentation: ["Piano"],
  },
  {
    id: 5,
    title: "The Four Seasons - Spring",
    composer: "Antonio Vivaldi",
    genre: "Baroque",
    difficulty: "Advanced",
    pages: 12,
    description: "First movement of Vivaldi's famous violin concerto. Full of energy and joy.",
    coverImage:
      "https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?auto=format&fit=crop&q=80&w=300&h=400",
    fileUrl: "/sheets/four-seasons-spring.pdf",
    uploadDate: "2024-01-18",
    instrumentation: ["Violin", "Orchestra"],
  },
  {
    id: 6,
    title: "Nocturne in E-flat major",
    composer: "Frédéric Chopin",
    genre: "Romantic",
    difficulty: "Advanced",
    pages: 5,
    description:
      "One of Chopin's most beautiful nocturnes, requiring expressive playing and good pedaling technique.",
    coverImage:
      "https://images.unsplash.com/photo-1507838153414-b4b713384a76?auto=format&fit=crop&q=80&w=300&h=400",
    fileUrl: "/sheets/chopin-nocturne.pdf",
    uploadDate: "2024-01-12",
    instrumentation: ["Piano"],
  },
];

export const genres: Genre[] = [
  { id: 1, name: "All", count: mockSheetMusic.length },
  { id: 2, name: "Classical", count: mockSheetMusic.filter((s) => s.genre === "Classical").length },
  { id: 3, name: "Baroque", count: mockSheetMusic.filter((s) => s.genre === "Baroque").length },
  { id: 4, name: "Romantic", count: mockSheetMusic.filter((s) => s.genre === "Romantic").length },
  {
    id: 5,
    name: "Impressionist",
    count: mockSheetMusic.filter((s) => s.genre === "Impressionist").length,
  },
  { id: 6, name: "Modern", count: mockSheetMusic.filter((s) => s.genre === "Modern").length },
];

export const difficulties = ["All", "Beginner", "Intermediate", "Advanced"];
