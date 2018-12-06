using System.Text;
using Windmill.Game;

namespace Windmill.Display
{
    public class AsciiGameRenderer
    {
        private const string AnsiBackgroundDark = "\u001b[48;5;8m";
        private const string AnsiBackgroundLight = "\u001b[48;5;9m";
        private const string AnsiForegroundDark = "\u001b[38;5;88m";
        private const string AnsiForegroundLight = "\u001b[38;5;214m";
        private const string AnsiBackgroundReset = "\u001b[0m";
        
        public string Render(GameState gameState)
        {
            StringBuilder sb = new StringBuilder();
            int backgroundColorMod = 0;

            sb.Append("  ");

            sb.AppendLine();

            for (var row = 8; row > 0; row--)
            {
                sb.Append(row+" "+(backgroundColorMod % 2 == 0 ? AnsiBackgroundDark : AnsiBackgroundLight));

                for (var column = 1; column <= 8; column++)
                {
                    sb.Append(backgroundColorMod % 2 == 0 ? AnsiBackgroundDark : AnsiBackgroundLight);
                    sb.Append(row > 2 ? AnsiForegroundDark : AnsiForegroundLight);
                    sb.Append("♜ ");

                    backgroundColorMod++;
                }

                backgroundColorMod--;

                sb.AppendLine(AnsiBackgroundReset);
            }

            sb.AppendLine("   a b c d e f g");
            
            /*
            sb.AppendLine("\u001b[31mHello World!\u001b[0m");
            sb.AppendLine("   ═══════════════");
            sb.AppendLine("8 ║♜ ♞ ♝ ♛ ♚ ♝ ♞ ♜");
            sb.AppendLine("7 ║♟ ♟ ♟ ♟ ♟ ♟ ♟ ♟");
            sb.AppendLine("6 ║░▓░▓░▓░▓");
            sb.AppendLine("5 ║… … … … … … … …");
            sb.AppendLine("4 ║… … … … … … … …");
            sb.AppendLine("3 ║… … ♘ … … … … …");
            sb.AppendLine("2 ║♙ ♙ ♙ ♙ ♙ ♙ ♙ ♙");
            sb.AppendLine("1 ║♖ … ♗ ♕ ♔ ♗ ♘ ♖");
            sb.AppendLine("  ╚═══════════════");
            sb.AppendLine("   a b c d e f g h");
            */
            
            return sb.ToString();
        }
    }
}