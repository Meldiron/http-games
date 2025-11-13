import type { BaseLayoutProps } from "fumadocs-ui/layouts/shared";
import Image from "next/image";

export function baseOptions(): BaseLayoutProps {
  return {
    githubUrl: "https://github.com/meldiron/http-games",
    nav: {
      title: (
        <>
          <Image
            src="/logo-on-dark.svg"
            alt="Logo"
            width={35}
            height={20}
            className="h-5 hidden dark:block"
          />
          <Image
            src="/logo-on-light.svg"
            alt="Logo"
            width={35}
            height={20}
            className="h-5 block dark:hidden"
          />
          HTTP Games
        </>
      ),
    },
  };
}
