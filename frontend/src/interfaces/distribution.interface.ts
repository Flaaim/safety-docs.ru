export interface UploadContactsDTO {
  file: File | null;
}

export interface FilesCollection {
  files: FileDTO[],
  total: number;
  currentPage: number;
  totalPages: number;
  perPage: number;
}

export interface FileDTO {
  id: string,
  name: string,
  date: string,
  complete: boolean
}
