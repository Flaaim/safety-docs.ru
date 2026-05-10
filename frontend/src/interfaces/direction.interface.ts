import {CategoryDTO} from "@/interfaces/category.interface";
import {Factory, Flame, HardHat, LucideIcon, Zap} from "lucide-react";

export interface DirectionCollection {
  directions: DirectionDTO[];
  total: number
}

export interface DirectionDTO{
  id: string,
  title: string,
  description: string,
  text: string
  slug: string
  categories: CategoryDTO[]
  icon: string;
}

export const IconsMap: Record<string, LucideIcon> = {
  'ohrana-truda': HardHat,
  'promyslennaa-bezopasnost': Factory,
  'pozarnaa-bezopasnost': Flame,
  'energeticeskaa-bezopasnost': Zap,
};


