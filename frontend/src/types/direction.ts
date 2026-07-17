import { Factory, Flame, HardHat, LucideIcon, Zap } from "lucide-react";
import type { Category } from "@/types/category";

/** Matches App\Template\Query\Direction\GetAll\DirectionDTO */
export interface Direction {
  id: string;
  title: string;
  description: string;
  text: string;
  slug: string;
}

export interface CategorySummary {
  id: string;
  title: string;
  description: string;
  slug: string;
}

/** Matches App\Template\Query\Direction\GetBySlug\DirectionDTO */
export interface DirectionWithCategories extends Direction {
  categories: CategorySummary[];
}

/** @deprecated Prefer Direction[]; kept for admin compatibility */
export interface DirectionCollection {
  directions: DirectionDTO[];
  total: number;
}

/** Admin/legacy shape — [directionSlug] optional when loaded from GetAll */
export type DirectionDTO = Direction & {
  icon?: string;
  categories?: Array<CategorySummary | Category>;
};

export const IconsMap: Record<string, LucideIcon> = {
  "ohrana-truda": HardHat,
  "promyslennaa-bezopasnost": Factory,
  "pozarnaa-bezopasnost": Flame,
  "energeticeskaa-bezopasnost": Zap,
};
