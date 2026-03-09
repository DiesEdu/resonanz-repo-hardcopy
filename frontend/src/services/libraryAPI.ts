import type { SheetMusic, User, UserRole } from "@/types";
import type { ApiResponse } from "@/types/api";

const API_BASE_URL: string = import.meta.env.VITE_API_BASE_URL || "http://localhost:3000/api";

interface UsersResponse {
  success: boolean;
  count: number;
  data: User[];
}

interface UserInput {
  name?: string;
  email: string;
  role: UserRole;
  password: string;
}

interface UserUpdateInput {
  name?: string;
  email?: string;
  role?: UserRole;
  password?: string;
}

interface RegisterInput {
  name?: string;
  email: string;
  password: string;
}

interface LoginInput {
  email: string;
  password: string;
}

class LibraryAPIService {
  static async request<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
    const url = `${API_BASE_URL}${endpoint}`;

    const config: RequestInit = {
      headers: {
        "Content-Type": "application/json",
        ...options.headers,
      },
      ...options,
    };

    if (config.body && typeof config.body === "object" && !(config.body instanceof FormData)) {
      config.body = JSON.stringify(config.body);
    }

    try {
      const response = await fetch(url, config);
      const text = await response.text();
      const data = text ? JSON.parse(text) : null;

      if (!response.ok) {
        throw new Error(data?.message || "API request failed");
      }

      return data as T;
    } catch (error) {
      console.error("API Error:", error);
      throw error;
    }
  }

  static async getScores(filters: Record<string, string | number | undefined> = {}) {
    const queryParams = new URLSearchParams();

    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        queryParams.append(key, String(value));
      }
    });

    const query = queryParams.toString() ? `?${queryParams.toString()}` : "";

    return this.request(`/library${query}`);
  }

  static async createScore(scoreData: SheetMusic): Promise<ApiResponse<SheetMusic>> {
    return this.request<ApiResponse<SheetMusic>>("/library", {
      method: "POST",
      body: JSON.stringify(scoreData),
    });
  }

  static async updateScore(
    scoreId: number,
    scoreData: Partial<SheetMusic>,
  ): Promise<ApiResponse<null>> {
    return this.request<ApiResponse<null>>(`/library/${scoreId}`, {
      method: "PUT",
      body: JSON.stringify(scoreData),
    });
  }

  static async getUsers(): Promise<UsersResponse> {
    return this.request<UsersResponse>("/users");
  }

  static async createUser(payload: UserInput): Promise<ApiResponse<User>> {
    return this.request<ApiResponse<User>>("/users", {
      method: "POST",
      body: JSON.stringify(payload),
    });
  }

  static async updateUser(userId: number, payload: UserUpdateInput): Promise<ApiResponse<null>> {
    return this.request<ApiResponse<null>>(`/users/${userId}`, {
      method: "PUT",
      body: JSON.stringify(payload),
    });
  }

  static async deleteUser(userId: number): Promise<ApiResponse<null>> {
    return this.request<ApiResponse<null>>(`/users/${userId}`, {
      method: "DELETE",
    });
  }

  static async register(payload: RegisterInput): Promise<ApiResponse<User>> {
    return this.request<ApiResponse<User>>("/auth/register", {
      method: "POST",
      body: JSON.stringify(payload),
    });
  }

  static async login(payload: LoginInput): Promise<ApiResponse<User>> {
    return this.request<ApiResponse<User>>("/auth/login", {
      method: "POST",
      body: JSON.stringify(payload),
    });
  }
}

export default LibraryAPIService;
