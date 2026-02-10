import {JSX} from "react";
import {SidebarProps} from "./Sidebar.props";
import {Htag, Ptag, Spantag, DefItem, DownloadButton, Deflisttag } from "@/components";
import styles from './Sidebar.module.css'
import classNames from "classnames";
import {ProductInfo} from "@/components/ProductInfo/ProductInfo";





export const Sidebar = ({className, ...props}:SidebarProps): JSX.Element => {
  return (
    <aside {...props} className={classNames(className, styles.sidebar)}>
      <Htag tag='h1'>Охрана труда документы 2026г.</Htag>
      <Ptag >Полный комплект локальных нормативных актов по охране труда</Ptag>
      <Ptag >Блог охраны труда <br />
        <Spantag ><a href="https://t.me/help_ot_news">https://t.me/help_ot_news</a>
        </Spantag>
      </Ptag>
      <Htag tag='h3'>Информация:</Htag>
      <ProductInfo productId='e2ff37fb-8690-46e5-82fa-75b5ceca8b61'></ProductInfo>
    </aside>)
}
