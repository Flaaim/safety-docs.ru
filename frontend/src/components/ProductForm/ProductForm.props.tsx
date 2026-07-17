import { DetailedHTMLProps, HTMLAttributes, ReactNode } from "react";

export interface ProductFormProps extends DetailedHTMLProps<
  HTMLAttributes<HTMLFormElement>,
  HTMLFormElement
> {
  headline?: ReactNode;
  /** Preferred payment document id */
  documentId?: string;
  /** @deprecated Use documentId */
  productId?: string;
  showTerms?: boolean;
}
