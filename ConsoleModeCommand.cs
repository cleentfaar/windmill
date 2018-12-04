using System.Collections.Generic;
using System.Linq;
using ManyConsole;

namespace Windmill
{
    public class ConsoleModeCommand : ManyConsole.ConsoleModeCommand
    {
        public ConsoleModeCommand()
        {
            this.IsCommand("console-mode", "Starts a console interface that allows multiple commands to be run.");
        }

        public override IEnumerable<ConsoleCommand> GetNextCommands()
        {
            return Program.GetCommands().Where(c => !(c is ConsoleModeCommand));
        }
    }
}