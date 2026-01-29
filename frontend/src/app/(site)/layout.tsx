import type { Metadata } from "next";
import { Noto_Sans } from "next/font/google";
import styles from './layout.module.css';
import "../globals.css";
import { Sidebar, Footer } from "@/components";




const notoSans = Noto_Sans({
  variable: "--font-noto-sans",
  subsets: ["cyrillic"],
});

export const metadata: Metadata = {
  title: "Блог",
  description: "Мой блог",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="ru">
      <body className={`${notoSans.variable} antialiased`}>
        <div className={styles.wrapper}>
          <header className={styles.header}>
            ХЕАДЕР
          </header>
          <Sidebar className={styles.sidebar} />
          <div className={styles.body}>
            {children}
          </div>
          <Footer className={styles.footer} />
        </div>
      </body>
    </html>
  );
}
