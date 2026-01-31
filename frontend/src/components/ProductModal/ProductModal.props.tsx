import {DetailedHTMLProps, HTMLAttributes, ReactNode} from "react";

export interface ProductModalProps extends DetailedHTMLProps<HTMLAttributes<HTMLDivElement>, HTMLDivElement> {
  children: ReactNode,
  isOpen: boolean
  onClose: () => void
}
