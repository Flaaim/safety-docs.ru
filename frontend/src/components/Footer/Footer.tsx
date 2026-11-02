import { JSX } from "react";
import classNames from "classnames";
import styles from "./Footer.module.css";
import { FooterProps } from "./Footer.props";
import { Ptag } from "@/components";
import { format } from "date-fns";

export const Footer = ({ className, ...props }: FooterProps): JSX.Element => {
  return (
    <footer {...props} className={classNames(className, styles.footer)}>
      <Ptag size="s">iot-1.ru {format(new Date(), "yyyy")}</Ptag>
      <Ptag size="s">Григорьев Александр Иванович, ИНН 272497691420. flaeim@gmail.com</Ptag>
    </footer>
  );
};
