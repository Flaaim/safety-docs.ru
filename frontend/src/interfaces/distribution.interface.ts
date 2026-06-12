export interface UploadContactsDTO {
  file: File | null;
}

export interface FilesCollection {
  files: FileDTO[];
  total: number;
  currentPage: number;
  totalPages: number;
  perPage: number;
}

export interface FileDTO {
  id: string;
  name: string;
  date: string;
  complete: boolean;
}
export type ProjectsCollection = {
  projects: ProjectsDTO[];
};
export interface ProjectsDTO {
  id: string;
  name: string;
  contact_count: number;
}

export interface NewslettersCollection {
  newsletters: Newsletter[];
  total: number;
  currentPage: number;
  totalPages: number;
  perPage: number;
}

export interface Newsletter {
  newsletterId: string;
  subject: string;
  templateId: string;
  projectName: string;
  createdAt: string;
  status: string;
}
