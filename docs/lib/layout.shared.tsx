import type { BaseLayoutProps } from "fumadocs-ui/layouts/shared";
import Image from "next/image";

export function baseOptions(): BaseLayoutProps {
  return {
    githubUrl: "https://github.com/meldiron/http-games",
    nav: {
      title: (
        <>
          <Image src="/logo-dark.svg" alt="Logo" className="h-5 hidden dark:block" />
          <Image
            src="/logo.svg"
            alt="Logo"
            className="h-5 block dark:hidden"
          />
          HTTP Games
        </>
      ),
    },
  };
}
