import {JSX} from "react";
import {SidebarProps} from "./Sidebar.props";
import {Tag, Htag, Ptag, Spantag, DefItem} from "@/components";
import classNames from "classnames";
import styles from './Sidebar.module.css'
import {Deflisttag} from "@/components/DeflistTag/Deflisttag";
export const Sidebar = ({className, ...props}:SidebarProps): JSX.Element => {
  return (
    <aside {...props} className={classNames(className, styles.sidebar)}>
      <Htag tag='h1'>Охрана труда документы 2026г.</Htag>
      <Ptag >Полный комплект локальных нормативных актов по охране труда</Ptag>
      <Ptag >Блог охраны труда <br />
        <Spantag ><a href="https://t.me/help_ot_news">https://t.me/help_ot_news</a>
        </Spantag>
      </Ptag>

      <Tag href='/'>
        <Spantag size='s' > Скачать </Spantag> <br />
        <Spantag appearance='bold' size='m'>RAR Архив</Spantag>
      </Tag>
      <Htag tag='h3'>Информация:</Htag>
      <hr/>
      <Deflisttag >
        <DefItem term='Стоимость' definition='1200 рублей' />
        <DefItem term='Формат' definition='docx, doc, ' />
        <DefItem term='Количество' definition='47 штук' />
      </Deflisttag>

    </aside>)
}
